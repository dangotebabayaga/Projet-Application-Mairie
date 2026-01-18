import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// Pages
import { LandingComponent } from './pages/landing/landing.component';
import { LoginComponent } from './pages/login/login.component';
import { RegisterComponent } from './pages/register/register.component';
import { HomeComponent } from './pages/home/home.component';
import { ReportsComponent } from './pages/reports/reports.component';
import { SurveysComponent } from './pages/surveys/surveys.component';
import { EventsComponent } from './pages/events/events.component';
import { DiscussionComponent } from './pages/discussion/discussion.component';
import { BackofficeComponent } from './pages/backoffice/backoffice.component';

const routes: Routes = [
  { path: '', component: LandingComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'home', component: HomeComponent },
  { path: 'reports', component: ReportsComponent },
  { path: 'surveys', component: SurveysComponent },
  { path: 'events', component: EventsComponent },
  { path: 'discussion', component: DiscussionComponent },
  { path: 'backoffice', component: BackofficeComponent },
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
