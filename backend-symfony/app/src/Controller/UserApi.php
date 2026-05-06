<?php
namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UtilisateursRepository;
use App\Repository\AdministrateursRepository;
use App\Repository\CitoyensRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtService;

#[Route('/api/utilisateur')]
class UserApi extends AbstractController
{

    private UtilisateursRepository $userRepo;
    private JwtService $jwtService;

    public function __construct(UtilisateursRepository $userRepo, JwtService $jwtService)
    {
        $this->userRepo = $userRepo;
        $this->jwtService = $jwtService;
    }

    #[Route('/register', methods:['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['motDePasse'])) {
            return $this->json([
                "error" => "Tous les champs obligatoires doivent être remplis"
            ], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                "error" => "L'adresse email n'est pas valide"
            ], 400);
        }

        // Vérifier si l'email existe déjà
        $existingUser = $this->userRepo->findByEmail($data['email']);
        if ($existingUser) {
            return $this->json([
                "error" => "Un compte avec cet email existe déjà"
            ], 409);
        }

        // Pour l'inscription publique (citoyens, role=1), quartier + catégorie sont obligatoires
        $role = (int) ($data['role'] ?? 1);
        if ($role === 1) {
            if (empty($data['quartierId']) || empty($data['categorieId'])) {
                return $this->json([
                    "error" => "Quartier et catégorie sont obligatoires"
                ], 400);
            }
        }

        $user = $this->userRepo->createUtilisateur($data);

        return $this->json([
            "message" => "Utilisateur créé",
            "id" => $user->getId()
        ]);
    }

    #[Route('/login', name:'api_login', methods:['POST'])]
    public function login(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $user = $this->userRepo->verifierConnexion($data['email'], $data['motDePasse']);

        if (!$user) {
            return $this->json([
                "error" => "Email ou mot de passe incorrect"
            ], 401);
        }

        $data2 = $this->userRepo->infoUser($user->getId());

        // génération du token
        $token = $this->jwtService->generateToken(
            $user->getId(),
            $user->getEmail(),
            $this->userRepo->getRole($user)
        );


        return $this->json([
            "message" => "Connexion réussie",
            "token" => $token,
            "infoUser" => $data2
        ]);
    }

    public function getUserFromToken(Request $request)
    {

        $header = $request->headers->get('Authorization');

        if (!$header) {
            return null;
        }

        $token = str_replace("Bearer ", "", $header);

        try {

            $decoded = $this->jwtService->decodeToken($token);

            return $this->userRepo->find($decoded->userId);

        } catch (\Exception $e) {

            return null;
        }
    }

    #[Route('/me', name: 'api_user_me', methods: ['GET'])]
    public function me(Request $request): JsonResponse
    {
        $user = $this->getUserFromToken($request);
        if (!$user) {
            return $this->json(["error" => "Non authentifié"], 401);
        }

        return $this->json($this->userRepo->infoUser($user->getId()));
    }

    #[Route('/me', name: 'api_user_me_update', methods: ['PUT'])]
    public function updateMe(Request $request): JsonResponse
    {
        $user = $this->getUserFromToken($request);
        if (!$user) {
            return $this->json(["error" => "Non authentifié"], 401);
        }

        $data = json_decode($request->getContent(), true) ?: [];

        if (!empty($data['email']) && $data['email'] !== $user->getEmail()) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json(["error" => "Email invalide"], 400);
            }
            $existing = $this->userRepo->findByEmail($data['email']);
            if ($existing && $existing->getId() !== $user->getId()) {
                return $this->json(["error" => "Cet email est déjà utilisé"], 409);
            }
        }

        $this->userRepo->updateUtilisateur($user, $data);
        $this->userRepo->updateCitoyenAffectation($user->getId(), $data);

        return $this->json([
            "message" => "Infos mises à jour",
            "infoUser" => $this->userRepo->infoUser($user->getId())
        ]);
    }

    #[Route('/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?: [];
        $email = trim($data['email'] ?? '');

        // Réponse identique dans tous les cas (anti-énumération)
        $genericResponse = $this->json([
            'message' => 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.'
        ]);

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $genericResponse;
        }

        $user = $this->userRepo->findByEmail($email);
        if (!$user) {
            return $genericResponse;
        }

        // Génération du token (32 octets random → 64 hex chars)
        $token = bin2hex(random_bytes(32));
        $expiresAt = new \DateTime('+1 hour');

        $reset = new PasswordResetToken();
        $reset->setUserId($user->getId());
        $reset->setToken($token);
        $reset->setExpiresAt($expiresAt);

        $em->persist($reset);
        $em->flush();

        // Lien envoyé au citoyen
        // URL de base configurable via env (APP_URL en prod = https://ton-domaine.fr)
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost:4200';
        $resetUrl = rtrim($baseUrl, '/') . '/reset-password?token=' . $token;

        // Expéditeur configurable via env (MAILER_FROM en prod = noreply@ton-domaine.fr)
        $fromEmail = $_ENV['MAILER_FROM'] ?? 'noreply@unicity.local';
        $mail = (new Email())
            ->from($fromEmail)
            ->to($email)
            ->subject('Réinitialisation de votre mot de passe UniCity')
            ->html(sprintf(
                '<p>Bonjour %s,</p>'
                . '<p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien ci-dessous pour choisir un nouveau mot de passe (valable 1 heure) :</p>'
                . '<p><a href="%s">Réinitialiser mon mot de passe</a></p>'
                . '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce mail.</p>'
                . '<p>— L\'équipe UniCity</p>',
                htmlspecialchars($user->getPrenom() ?? ''),
                $resetUrl
            ));

        try {
            $mailer->send($mail);
        } catch (\Throwable $e) {
            // En cas de souci SMTP, on ne révèle pas l'erreur côté client
        }

        return $genericResponse;
    }

    #[Route('/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        EntityManagerInterface $em,
        PasswordResetTokenRepository $tokenRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?: [];
        $token = trim($data['token'] ?? '');
        $newPassword = $data['motDePasse'] ?? '';

        if (!$token) {
            return $this->json(['error' => 'Token manquant'], 400);
        }
        if (strlen($newPassword) < 6) {
            return $this->json(['error' => 'Le mot de passe doit faire au moins 6 caractères'], 400);
        }

        $reset = $tokenRepo->findValidByToken($token);
        if (!$reset) {
            return $this->json(['error' => 'Lien invalide ou expiré'], 400);
        }

        $user = $this->userRepo->find($reset->getUserId());
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $user->setMotDePasseHash(password_hash($newPassword, PASSWORD_BCRYPT));
        $reset->setUsed(true);

        $em->flush();

        return $this->json(['message' => 'Mot de passe réinitialisé avec succès']);
    }

}