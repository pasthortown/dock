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

         //NETMONITOR

         {
            path: 'target',
            loadChildren: './CRUD/NETMONITOR/Target/target.module#TargetModule'
         },
         {
            path: 'statistic',
            loadChildren: './CRUD/NETMONITOR/Statistic/statistic.module#StatisticModule'
         },
         {
            path: 'monitoring_tool',
            loadChildren: './CRUD/NETMONITOR/MonitoringTool/monitoringtool.module#MonitoringToolModule'
         },
         {
            path: 'target_type',
            loadChildren: './CRUD/NETMONITOR/TargetType/targettype.module#TargetTypeModule'
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