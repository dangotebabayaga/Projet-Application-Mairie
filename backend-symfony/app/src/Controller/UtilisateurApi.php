<?php
namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Citoyens;
use App\Entity\SuperAdministrateur;
use App\Entity\Utilisateurs;
use App\Repository\AdministrateursRepository;
use App\Repository\CitoyensRepository;
use App\Repository\SuperAdministrateurRepository;
use App\Repository\UtilisateursRepository;
use App\Service\AuthChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/utilisateurs')]
class UtilisateurApi extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UtilisateursRepository $userRepo,
        private AuthChecker $auth
    ) {}

    #[Route('', name: 'list_users', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $err = $this->requireSuperAdmin($request);
        if ($err) return $err;

        $users = $this->userRepo->findAll();
        $data = array_map(function (Utilisateurs $u) {
            return [
                'id' => $u->getId(),
                'nom' => $u->getNom(),
                'prenom' => $u->getPrenom(),
                'email' => $u->getEmail(),
                'telephone' => $u->getTelephone(),
                'role' => $this->userRepo->getRole($u),
                'compteActif' => method_exists($u, 'isCompteActif') ? $u->isCompteActif() : true,
                'dateCreation' => $u->getDateCreation()
            ];
        }, $users);
        return $this->json($data);
    }

    #[Route('', name: 'create_user', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $err = $this->requireSuperAdmin($request);
        if ($err) return $err;

        $data = json_decode($request->getContent(), true) ?: [];
        foreach (['nom', 'prenom', 'email', 'motDePasse', 'role'] as $f) {
            if (empty($data[$f])) {
                return $this->json(['error' => "Champ obligatoire manquant: $f"], 400);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => "Email invalide"], 400);
        }

        if ($this->userRepo->findByEmail($data['email'])) {
            return $this->json(['error' => 'Un compte avec cet email existe déjà'], 409);
        }

        $role = (int) $data['role'];
        if (!in_array($role, [1, 2], true)) {
            return $this->json(['error' => 'Rôle invalide (1=citoyen, 2=admin)'], 400);
        }

        $user = $this->userRepo->createUtilisateur($data);

        return $this->json([
            'message' => 'Utilisateur créé',
            'id' => $user->getId()
        ]);
    }

    #[Route('/{id}/role', name: 'change_user_role', methods: ['PUT'])]
    public function changeRole(Request $request, int $id): JsonResponse
    {
        $err = $this->requireSuperAdmin($request, $currentUser);
        if ($err) return $err;

        if ($id === (int) $currentUser['userId']) {
            return $this->json(['error' => 'Vous ne pouvez pas modifier votre propre rôle'], 400);
        }

        $user = $this->userRepo->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true) ?: [];
        $newRole = $data['role'] ?? null;
        if (!in_array($newRole, ['citoyen', 'admin'], true)) {
            return $this->json(['error' => 'Rôle invalide (citoyen ou admin uniquement)'], 400);
        }

        $citoyenRepo = $this->em->getRepository(Citoyens::class);
        $adminRepo = $this->em->getRepository(Admin::class);

        if ($newRole === 'admin') {
            // Promotion vers admin : on ajoute juste la ligne admin (on garde
            // l'éventuelle ligne citoyens pour préserver les FK des signalements)
            if (!$adminRepo->find($id)) {
                $a = new Admin();
                $a->setUtilisateurId($id);
                $this->em->persist($a);
            }
        } else {
            // Rétrogradation vers citoyen : on retire la ligne admin (pas de FK qui dépend)
            // et on s'assure que la ligne citoyens existe
            if ($existing = $adminRepo->find($id)) {
                $this->em->remove($existing);
            }
            if (!$citoyenRepo->find($id)) {
                $c = new Citoyens();
                $c->setUtilisateurId($id);
                $this->em->persist($c);
            }
        }
        $this->em->flush();

        return $this->json(['message' => 'Rôle mis à jour', 'role' => $newRole]);
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $err = $this->requireSuperAdmin($request, $currentUser);
        if ($err) return $err;

        if ($id === (int) $currentUser['userId']) {
            return $this->json(['error' => 'Vous ne pouvez pas modifier votre propre compte via cette API'], 400);
        }

        $user = $this->userRepo->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true) ?: [];

        // Vérif email unique si changement
        if (!empty($data['email']) && $data['email'] !== $user->getEmail()) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json(['error' => 'Email invalide'], 400);
            }
            $existing = $this->userRepo->findByEmail($data['email']);
            if ($existing && $existing->getId() !== $id) {
                return $this->json(['error' => 'Un compte avec cet email existe déjà'], 409);
            }
        }

        $this->userRepo->updateUtilisateur($user, $data);

        // Réinitialisation du mot de passe (optionnelle)
        if (!empty($data['motDePasse'])) {
            if (strlen($data['motDePasse']) < 6) {
                return $this->json(['error' => 'Le mot de passe doit faire au moins 6 caractères'], 400);
            }
            $user->setMotDePasseHash(password_hash($data['motDePasse'], PASSWORD_BCRYPT));
            $this->em->flush();
        }

        return $this->json(['message' => 'Utilisateur mis à jour']);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function delete(Request $request, int $id): JsonResponse
    {
        $err = $this->requireSuperAdmin($request, $currentUser);
        if ($err) return $err;

        if ($id === (int) $currentUser['userId']) {
            return $this->json(['error' => 'Vous ne pouvez pas vous supprimer'], 400);
        }

        $user = $this->userRepo->find($id);
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        // Empêcher de supprimer un superadmin
        $superRepo = $this->em->getRepository(SuperAdministrateur::class);
        if ($superRepo->find($id)) {
            return $this->json(['error' => 'Un superadmin ne peut être supprimé via cette API'], 403);
        }

        // Détacher des tables d'héritage avant
        $citoyenRepo = $this->em->getRepository(Citoyens::class);
        $adminRepo = $this->em->getRepository(Admin::class);
        if ($existing = $citoyenRepo->find($id)) $this->em->remove($existing);
        if ($existing = $adminRepo->find($id)) $this->em->remove($existing);
        $this->em->flush();

        $this->em->remove($user);
        $this->em->flush();

        return $this->json(['message' => 'Utilisateur supprimé']);
    }

    private function requireSuperAdmin(Request $request, &$user = null): ?JsonResponse
    {
        $user = $this->auth->getUserFromRequest($request);
        if (!$user) {
            return $this->json(['error' => 'Token manquant ou invalide'], 401);
        }
        if (($user['role'] ?? null) !== 'superadmin') {
            return $this->json(['error' => 'Accès interdit (superadmin requis)'], 403);
        }
        return null;
    }
}
