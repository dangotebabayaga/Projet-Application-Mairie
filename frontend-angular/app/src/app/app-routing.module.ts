import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', loadComponent: () => import('./pages/login/login.component').then(m => m.LoginComponent) },
  { path: 'register', loadComponent: () => import('./pages/register/register.component').then(m => m.RegisterComponent) },
  { path: 'home', loadComponent: () => import('./pages/home/home.component').then(m => m.HomeComponent), canActivate: [AuthGuard] },
  { path: 'reports', loadComponent: () => import('./pages/reports/reports.component').then(m => m.ReportsComponent), canActivate: [AuthGuard] },
  { path: 'surveys', loadComponent: () => import('./pages/surveys/surveys.component').then(m => m.SurveysComponent), canActivate: [AuthGuard] },
  { path: 'events', loadComponent: () => import('./pages/events/events.component').then(m => m.EventsComponent), canActivate: [AuthGuard] },
  { path: 'discussion', loadComponent: () => import('./pages/discussion/discussion.component').then(m => m.DiscussionComponent), canActivate: [AuthGuard] },
  { path: 'backoffice', loadComponent: () => import('./pages/backoffice/backoffice.component').then(m => m.BackofficeComponent), canActivate: [AuthGuard] },
  { path: '**', redirectTo: 'login' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
