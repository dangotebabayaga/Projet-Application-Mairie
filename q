[1mdiff --git a/frontend-angular/app/src/app/pages/events/events.component.ts b/frontend-angular/app/src/app/pages/events/events.component.ts[m
[1mindex aaa75119..1fa2d514 100644[m
[1m--- a/frontend-angular/app/src/app/pages/events/events.component.ts[m
[1m+++ b/frontend-angular/app/src/app/pages/events/events.component.ts[m
[36m@@ -32,12 +32,8 @@[m [mexport class EventsComponent implements OnInit {[m
     this.userRole = this.roles[0];[m
   }[m
 [m
[31m-  get isadministrateur(): boolean {[m
[31m-      return this.roles.includes('administrateur');[m
[31m-  }[m
[31m-  [m
[31m-  get isElu(): boolean {[m
[31m-      return this.roles.includes('elu');[m
[32m+[m[32m  get isAdmin(): boolean {[m
[32m+[m[32m      return this.userRole.includes('administrateur');[m
   }[m
 [m
   ngOnInit(): void {[m
[36m@@ -92,6 +88,12 @@[m [mexport class EventsComponent implements OnInit {[m
     }[m
   }[m
 [m
[32m+[m[32m  showCreateForm = false;[m
[32m+[m
[32m+[m[32m  toggleCreateForm(): void {[m
[32m+[m[32m      this.showCreateForm = !this.showCreateForm;[m
[32m+[m[32m  }[m
[32m+[m
   isEventSaved(index: number): boolean {[m
     return this.savedEvents.includes(index.toString());[m
   }[m
