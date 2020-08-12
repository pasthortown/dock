import { HTTP } from '@ionic-native/http/ngx';
import { SidebarComponent } from './components/sidebar/sidebar.component';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { LayoutRoutingModule } from './layout-routing.module';
import { LayoutComponent } from './layout.component';

import { IonicModule } from '@ionic/angular';
import { ProfilePictureService } from '../services/profile/profilepicture.service';
import { UserService } from 'src/app/services/profile/user.service';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    LayoutRoutingModule,
  ],
  declarations: [LayoutComponent, SidebarComponent],
  providers: [ProfilePictureService, UserService, HTTP]
})
export class LayoutModule {}
