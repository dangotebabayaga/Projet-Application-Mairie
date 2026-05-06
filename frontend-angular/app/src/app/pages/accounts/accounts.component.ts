import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AccountService, AccountUser, CreateAccountPayload, UpdateAccountPayload } from '../../services/account.service';
import { AuthService } from '../../services/auth.service';

interface AccountForm {
  nom: string;
  prenom: string;
  email: string;
  motDePasse: string;
  role: 1 | 2;
}

@Component({
  selector: 'app-accounts',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './accounts.component.html',
  styleUrls: ['./accounts.component.scss']
})
export class AccountsComponent implements OnInit {
  users: AccountUser[] = [];
  loading = false;
  loadError: string | null = null;

  showCreateForm = false;
  creating = false;
  createError: string | null = null;
  createSuccess: string | null = null;

  // Modal de détail
  selectedUser: AccountUser | null = null;
  detailEditing = false;
  detailSaving = false;
  detailError: string | null = null;

  // Envoi mail de réinitialisation
  resetSending = false;
  resetMessage: string | null = null;
  resetError: string | null = null;

  form: AccountForm = this.emptyForm();

  selectedRoleFilter: 'all' | 'citoyen' | 'admin' | 'superadmin' = 'all';

  currentUserId: number;

  private pendingDetailId: number | null = null;

  constructor(
    private accountService: AccountService,
    private auth: AuthService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.currentUserId = Number(localStorage.getItem('userId')) || 0;
  }

  ngOnInit(): void {
    const role = localStorage.getItem('userRole');
    if (role !== 'superadmin') {
      this.router.navigate(['/home']);
      return;
    }
    this.loadUsers();

    // Sync modal détail avec :id dans l'URL (gère bouton retour navigateur)
    this.route.paramMap.subscribe(params => {
      const idStr = params.get('id');
      this.pendingDetailId = idStr ? Number(idStr) : null;
      this.applyDetailFromRoute();
    });
  }

  private applyDetailFromRoute(): void {
    if (this.pendingDetailId === null) {
      this.selectedUser = null;
      this.detailEditing = false;
      this.detailError = null;
      this.resetMessage = null;
      this.resetError = null;
      return;
    }
    if (this.users.length === 0) return; // attendre la fin du chargement
    const user = this.users.find(u => u.id === this.pendingDetailId);
    if (user) {
      this.openDetailLocal(user);
    } else {
      this.router.navigate(['/comptes']);
    }
  }

  private openDetailLocal(user: AccountUser): void {
    this.selectedUser = user;
    this.detailEditing = false;
    this.detailError = null;
    this.resetMessage = null;
    this.resetError = null;
    this.form = {
      nom: user.nom,
      prenom: user.prenom,
      email: user.email,
      motDePasse: '',
      role: user.role === 'admin' ? 2 : 1
    };
  }

  loadUsers(): void {
    this.loading = true;
    this.loadError = null;
    this.accountService.getAll().subscribe({
      next: (data) => {
        this.users = data;
        this.loading = false;
        // Si la modal a été demandée par URL avant que la liste arrive, l'ouvrir maintenant
        if (this.pendingDetailId !== null && !this.selectedUser) {
          this.applyDetailFromRoute();
        }
      },
      error: (err) => {
        this.loading = false;
        this.loadError = err.error?.error || 'Erreur lors du chargement';
      }
    });
  }

  get filteredUsers(): AccountUser[] {
    if (this.selectedRoleFilter === 'all') return this.users;
    return this.users.filter(u => u.role === this.selectedRoleFilter);
  }

  selectRoleFilter(role: 'all' | 'citoyen' | 'admin' | 'superadmin'): void {
    this.selectedRoleFilter = role;
  }

  countByRole(role: 'citoyen' | 'admin' | 'superadmin'): number {
    return this.users.filter(u => u.role === role).length;
  }

  toggleCreate(): void {
    this.showCreateForm = !this.showCreateForm;
    if (this.showCreateForm) {
      this.form = this.emptyForm();
      this.createError = null;
      this.createSuccess = null;
    }
  }

  // ---- Modal de détail ----

  openDetail(user: AccountUser): void {
    // Navigation via URL pour que le bouton retour navigateur ferme la modal
    this.router.navigate(['/comptes', user.id]);
  }

  closeDetail(): void {
    this.router.navigate(['/comptes']);
  }

  sendResetEmail(): void {
    if (!this.selectedUser) return;
    if (this.resetSending) return;
    if (!confirm(`Envoyer un mail de réinitialisation de mot de passe à ${this.selectedUser.email} ?`)) return;

    this.resetSending = true;
    this.resetMessage = null;
    this.resetError = null;
    this.auth.forgotPassword(this.selectedUser.email).subscribe({
      next: () => {
        this.resetSending = false;
        this.resetMessage = `Mail envoyé à ${this.selectedUser?.email}`;
      },
      error: (err) => {
        this.resetSending = false;
        this.resetError = err.error?.error || "Erreur lors de l'envoi du mail";
      }
    });
  }

  startDetailEdit(): void {
    if (!this.selectedUser) return;
    if (this.selectedUser.id === this.currentUserId) return;
    this.detailEditing = true;
    this.detailError = null;
  }

  cancelDetailEdit(): void {
    if (!this.selectedUser) return;
    this.detailEditing = false;
    this.detailError = null;
    this.form = {
      nom: this.selectedUser.nom,
      prenom: this.selectedUser.prenom,
      email: this.selectedUser.email,
      motDePasse: '',
      role: this.selectedUser.role === 'admin' ? 2 : 1
    };
  }

  saveDetail(): void {
    if (!this.selectedUser) return;
    this.detailError = null;

    if (!this.form.nom.trim() || !this.form.prenom.trim() || !this.form.email.trim()) {
      this.detailError = 'Nom, prénom et email sont obligatoires';
      return;
    }

    const payload: UpdateAccountPayload = {
      nom: this.form.nom.trim(),
      prenom: this.form.prenom.trim(),
      email: this.form.email.trim()
    };

    this.detailSaving = true;
    this.accountService.update(this.selectedUser.id, payload).subscribe({
      next: () => {
        this.detailSaving = false;
        this.detailEditing = false;
        // Mettre à jour l'utilisateur affiché localement
        if (this.selectedUser) {
          this.selectedUser.nom = payload.nom!;
          this.selectedUser.prenom = payload.prenom!;
          this.selectedUser.email = payload.email!;
        }
        this.loadUsers();
      },
      error: (err) => {
        this.detailSaving = false;
        this.detailError = err.error?.error || 'Erreur lors de la mise à jour';
      }
    });
  }

  onSubmit(): void {
    this.onCreate();
  }

  private onCreate(): void {
    this.createError = null;
    this.createSuccess = null;

    if (!this.form.nom.trim() || !this.form.prenom.trim() || !this.form.email.trim() || !this.form.motDePasse) {
      this.createError = 'Tous les champs sont obligatoires';
      return;
    }
    if (this.form.motDePasse.length < 6) {
      this.createError = 'Le mot de passe doit faire au moins 6 caractères';
      return;
    }

    const payload: CreateAccountPayload = {
      nom: this.form.nom.trim(),
      prenom: this.form.prenom.trim(),
      email: this.form.email.trim(),
      motDePasse: this.form.motDePasse,
      role: this.form.role
    };

    this.creating = true;
    this.accountService.create(payload).subscribe({
      next: () => {
        this.creating = false;
        this.createSuccess = `${this.form.role === 1 ? 'Citoyen' : 'Élu'} créé avec succès`;
        this.form = this.emptyForm();
        this.loadUsers();
        setTimeout(() => this.createSuccess = null, 3000);
      },
      error: (err) => {
        this.creating = false;
        this.createError = err.error?.error || 'Erreur lors de la création';
      }
    });
  }

  promote(user: AccountUser): void {
    if (user.role !== 'citoyen') return;
    if (!confirm(`Promouvoir ${user.prenom} ${user.nom} en élu ?`)) return;
    this.accountService.changeRole(user.id, 'admin').subscribe({
      next: () => {
        if (this.selectedUser?.id === user.id) this.selectedUser.role = 'admin';
        this.loadUsers();
      },
      error: (err) => alert(err.error?.error || 'Erreur')
    });
  }

  demote(user: AccountUser): void {
    if (user.role !== 'admin') return;
    if (!confirm(`Rétrograder ${user.prenom} ${user.nom} en citoyen ?`)) return;
    this.accountService.changeRole(user.id, 'citoyen').subscribe({
      next: () => {
        if (this.selectedUser?.id === user.id) this.selectedUser.role = 'citoyen';
        this.loadUsers();
      },
      error: (err) => alert(err.error?.error || 'Erreur')
    });
  }

  deleteUser(user: AccountUser): void {
    if (user.id === this.currentUserId) return;
    if (user.role === 'superadmin') return;
    if (!confirm(`Supprimer définitivement ${user.prenom} ${user.nom} ?`)) return;
    this.accountService.delete(user.id).subscribe({
      next: () => {
        this.closeDetail();
        this.loadUsers();
      },
      error: (err) => alert(err.error?.error || 'Erreur')
    });
  }

  roleLabel(role: string): string {
    switch (role) {
      case 'superadmin': return 'Super-admin';
      case 'admin': return 'Élu';
      case 'citoyen': return 'Citoyen';
      default: return role;
    }
  }

  formatDate(date: string | null): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR');
  }

  private emptyForm(): AccountForm {
    return {
      nom: '',
      prenom: '',
      email: '',
      motDePasse: '',
      role: 1
    };
  }
}
