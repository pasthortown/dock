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

         //LSTracking

         {
            path: 'mobile_type',
            loadChildren: './CRUD/LSTRACKING/MobileType/mobiletype.module#MobileTypeModule'
         },
         {
            path: 'mobile_attachment',
            loadChildren: './CRUD/LSTRACKING/MobileAttachment/mobileattachment.module#MobileAttachmentModule'
         },
         {
            path: 'mobile',
            loadChildren: './CRUD/LSTRACKING/Mobile/mobile.module#MobileModule'
         },
         {
            path: 'mobile_position',
            loadChildren: './CRUD/LSTRACKING/MobilePosition/mobileposition.module#MobilePositionModule'
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