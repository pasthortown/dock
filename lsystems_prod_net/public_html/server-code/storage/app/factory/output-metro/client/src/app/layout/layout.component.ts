import { Component, OnInit } from '@angular/core';
import { ProfilePicture } from 'src/app/models/profile/ProfilePicture';
import { User } from 'src/app/models/profile/User';
import { UserService } from 'src/app/services/profile/user.service';
import { ProfilePictureService } from 'src/app/services/profile/profilepicture.service';

@Component({
    selector: 'app-layout',
    templateUrl: './layout.component.html',
    styleUrls: ['./layout.component.scss']
})
export class LayoutComponent implements OnInit {
    collapedSideBar: boolean;
    user = new User();
    profile_picture = new ProfilePicture();

    constructor(public profilePictureDataService: ProfilePictureService, private userDataService: UserService) {}

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
        this.profilePictureDataService.get(this.user.id).then( r2 => {
            if (typeof r2.id !==  'undefined') {
                this.profile_picture = r2 as ProfilePicture;
            }
        }).catch( e => { console.log(e); });
    }

    receiveCollapsed($event) {
        this.collapedSideBar = $event;
    }
}
