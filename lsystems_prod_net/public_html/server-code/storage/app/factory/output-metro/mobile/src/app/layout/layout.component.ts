import { Component, OnInit } from '@angular/core';
import { ProfilePictureService } from '../services/profile/profilepicture.service';
import { ProfilePicture } from '../models/profile/ProfilePicture';
import { UserService } from 'src/app/services/profile/user.service';
import { User } from 'src/app/models/profile/User';

@Component({
  selector: 'app-layout',
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.scss'],
})
export class LayoutComponent implements OnInit {
  user = new User();
  profile_picture = new ProfilePicture();

  constructor(private profilePictureDataService: ProfilePictureService, private userDataService: UserService) { }

  ngOnInit() {
    this.getUserInfo();
  }

  getUserInfo() {
    const userData = JSON.parse(sessionStorage.getItem('user'));
    this.userDataService.get(userData.id).then( r => {
        this.user = r as User;
        this.getProfilePicture();
    }).catch( e => { console.log(e); });
  }

  getProfilePicture() {
    this.profilePictureDataService.get(this.user.id).then( r => {
        const response = r;
        if (typeof response.id !==  'undefined') {
            this.profile_picture = r as ProfilePicture
        }
    }).catch( e => { console.log(e); });
  }

}
