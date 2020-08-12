import { Router } from '@angular/router';
import { User } from 'src/app/models/profile/User';
import { Component, OnInit, Input } from '@angular/core';
import { ProfilePicture } from 'src/app/models/profile/ProfilePicture';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})
export class SidebarComponent implements OnInit {
  @Input('profile_picture') profile_picture = new ProfilePicture();
  @Input('user') user = new User();
  profileImg = 'assets/images/accounts.png';
  appName = 'APPNAME';

  constructor(private router: Router) { }

  ngOnInit() {
  }

  ngOnChanges() {
    this.refreshUser();
  }

  refreshUser() {
    if ( this.profile_picture.id == 0 ) {
      this.profileImg = 'assets/images/accounts.png';
    } else {
      this.profileImg = 'data:' + this.profile_picture.file_type + ';base64,' + this.profile_picture.file;
    }
  }

  logout() {
    sessionStorage.clear();
    this.router.navigate(['/login']);
  }
}
