import { ProfilePictureService } from './../services/profile/profilepicture.service';
import { AuthService } from './../services/auth.service';
import { HTTP } from '@ionic-native/http/ngx';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { LoginPage } from './login.page';

const routes: Routes = [
  {
    path: '',
    component: LoginPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RouterModule.forChild(routes)
  ],
  declarations: [LoginPage],
  providers: [AuthService, ProfilePictureService, HTTP]
})
export class LoginPageModule {}
