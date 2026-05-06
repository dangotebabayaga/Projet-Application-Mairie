import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./pages/register/register.component').then(m => m.RegisterComponent) },
  { path: 'forgot-password', loadComponent: () => import('./pages/forgot-password/forgot-password.component').then(m => m.ForgotPasswordComponent) },
  { path: 'reset-password', loadComponent: () => import('./pages/reset-password/reset-password.component').then(m => m.ResetPasswordComponent) },
  { path: 'home', loadComponent: () => import('./pages/home/home.component').then(m => m.HomeComponent), canActivate: [AuthGuard] },
  { path: 'reports', loadComponent: () => import('./pages/reports/reports.component').then(m => m.ReportsComponent), canActivate: [AuthGuard] },
  { path: 'reports/:id', loadComponent: () => import('./pages/reports/report-detail/report-detail.component').then(m => m.ReportDetailComponent), canActivate: [AuthGuard] },
  { path: 'surveys', loadComponent: () => import('./pages/surveys/surveys.component').then(m => m.SurveysComponent), canActivate: [AuthGuard] },
  { path: 'surveys/:id', loadComponent: () => import('./pages/surveys/survey-detail/survey-detail.component').then(m => m.SurveyDetailComponent), canActivate: [AuthGuard] },
  { path: 'events', loadComponent: () => import('./pages/events/events.component').then(m => m.EventsComponent), canActivate: [AuthGuard] },
  { path: 'events/:id', loadComponent: () => import('./pages/events/event-detail/event-detail.component').then(m => m.EventDetailComponent), canActivate: [AuthGuard] },
  { path: 'discussion', loadComponent: () => import('./pages/discussion/discussion.component').then(m => m.DiscussionComponent), canActivate: [AuthGuard] },
  { path: 'backoffice', loadComponent: () => import('./pages/backoffice/backoffice.component').then(m => m.BackofficeComponent), canActivate: [AuthGuard] },
  { path: 'backoffice/:tab', loadComponent: () => import('./pages/backoffice/backoffice.component').then(m => m.BackofficeComponent), canActivate: [AuthGuard] },
  { path: 'settings', loadComponent: () => import('./pages/settings/settings.component').then(m => m.SettingsComponent), canActivate: [AuthGuard] },
  { path: 'comptes', loadComponent: () => import('./pages/accounts/accounts.component').then(m => m.AccountsComponent), canActivate: [AuthGuard] },
  { path: 'comptes/:id', loadComponent: () => import('./pages/accounts/accounts.component').then(m => m.AccountsComponent), canActivate: [AuthGuard] },
  { path: 'profile', loadComponent: () => import('./pages/profile/profile.component').then(m => m.ProfileComponent), canActivate: [AuthGuard] },
  { path: '**', redirectTo: 'login' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
