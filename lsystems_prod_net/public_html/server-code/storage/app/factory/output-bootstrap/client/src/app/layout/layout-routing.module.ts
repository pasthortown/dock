import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LayoutComponent } from './layout.component';

const routes: Routes = [
   {
      path: '',
      component: LayoutComponent,
      children: [
         {
            path: '',
            redirectTo: 'main'
         },
         {
            path: 'main',
            loadChildren: './main/main.module#MainModule'
         },
         {
            path: 'profile',
            loadChildren: './profile/profile.module#ProfileModule'
         },

         //videoconference

         {
            path: 'resource_type',
            loadChildren: './CRUD/VIDEOCONFERENCE/ResourceType/resourcetype.module#ResourceTypeModule'
         },
         {
            path: 'schedule_type',
            loadChildren: './CRUD/VIDEOCONFERENCE/ScheduleType/scheduletype.module#ScheduleTypeModule'
         },
         {
            path: 'guest_type',
            loadChildren: './CRUD/VIDEOCONFERENCE/GuestType/guesttype.module#GuestTypeModule'
         },
         {
            path: 'resource',
            loadChildren: './CRUD/VIDEOCONFERENCE/Resource/resource.module#ResourceModule'
         },
         {
            path: 'schedule',
            loadChildren: './CRUD/VIDEOCONFERENCE/Schedule/schedule.module#ScheduleModule'
         },
         {
            path: 'guest',
            loadChildren: './CRUD/VIDEOCONFERENCE/Guest/guest.module#GuestModule'
         },
         {
            path: 'responsable',
            loadChildren: './CRUD/VIDEOCONFERENCE/Responsable/responsable.module#ResponsableModule'
         },
         {
            path: 'schedule_resource_assigment',
            loadChildren: './CRUD/VIDEOCONFERENCE/ScheduleResourceAssigment/scheduleresourceassigment.module#ScheduleResourceAssigmentModule'
         },
         {
            path: 'schedule_resource_assistant',
            loadChildren: './CRUD/VIDEOCONFERENCE/ScheduleResourceAssistant/scheduleresourceassistant.module#ScheduleResourceAssistantModule'
         },
         {
            path: 'schedule_responsable_assigment',
            loadChildren: './CRUD/VIDEOCONFERENCE/ScheduleResponsableAssigment/scheduleresponsableassigment.module#ScheduleResponsableAssigmentModule'
         },
         {
            path: 'role',
            loadChildren: './CRUD/VIDEOCONFERENCE/Role/role.module#RoleModule'
         },
         {
            path: 'blank',
            loadChildren: './blank-page/blank-page.module#BlankPageModule'
         },
         {
            path: 'not-found',
            loadChildren: './not-found/not-found.module#NotFoundModule'
         },
         {
            path: '**',
            redirectTo: 'not-found'
         }
      ]
   }
];

@NgModule({
   imports: [RouterModule.forChild(routes)],
   exports: [RouterModule]
})
export class LayoutRoutingModule {}