import { Router, NavigationEnd } from '@angular/router';
import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { ProfilePicture } from 'src/app/models/profile/ProfilePicture';
import { User } from 'src/app/models/profile/User';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss']
})
export class SidebarComponent implements OnInit {
  @Input('profile_picture') profile_picture = new ProfilePicture();
  @Input('user') user = new User();

  isActive: boolean;
  collapsed: boolean;
  showMenu: string;
  pushRightClass: string;
  profileImg = 'assets/images/accounts.png';

  @Output() collapsedEvent = new EventEmitter<boolean>();

  constructor(private router: Router) {
    this.router.events.subscribe(val => {
      if (
        val instanceof NavigationEnd &&
        window.innerWidth <= 992 &&
        this.isToggled()
      ) {
        this.toggleSidebar();
      }
    });
  }

  ngOnInit() {
    this.isActive = false;
    this.collapsed = false;
    this.showMenu = '';
    this.pushRightClass = 'push-right';
  }

  ngOnChanges() {
    this.refreshUser();
  }

  eventCalled() {
    this.isActive = !this.isActive;
  }

  addExpandClass(element: any) {
    if (element === this.showMenu) {
      this.showMenu = '0';
    } else {
      this.showMenu = element;
    }
  }

  isToggled(): boolean {
    const dom: Element = document.querySelector('body');
    return dom.classList.contains(this.pushRightClass);
  }

  toggleSidebar() {
    const dom: any = document.querySelector('body');
    dom.classList.toggle(this.pushRightClass);
  }

  logOut() {
    sessionStorage.clear();
    this.router.navigate(['/login']);
  }

  refreshUser() {
    if ( this.profile_picture.id == 0 ) {
      this.profileImg = 'assets/images/accounts.png';
    } else {
      this.profileImg = 'data:' + this.profile_picture.file_type + ';base64,' + this.profile_picture.file;
    }
  }
}
