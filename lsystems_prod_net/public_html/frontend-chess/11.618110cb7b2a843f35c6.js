(window.webpackJsonp=window.webpackJsonp||[]).push([[11],{"/cdV":function(n,l,t){"use strict";t.r(l);var e=t("CcnG"),o=function(){return function(){}}(),u=t("pMnS"),r=t("4GxJ"),i=t("Ip0R"),a=t("ZYCi"),s=t("AytR"),c=function(){function n(n){var l=this;this.router=n,this.profileImg="assets/images/accounts.png",this.appName=s.a.app_name,this.router.events.subscribe(function(n){n instanceof a.d&&window.innerWidth<=992&&l.isToggled()&&l.toggleSidebar()})}return n.prototype.ngOnInit=function(){this.pushRightClass="push-right",this.user=JSON.parse(sessionStorage.getItem("user"))},n.prototype.isToggled=function(){return document.querySelector("body").classList.contains(this.pushRightClass)},n.prototype.toggleSidebar=function(){document.querySelector("body").classList.toggle(this.pushRightClass)},n.prototype.logout=function(){sessionStorage.clear(),this.router.navigate(["/login"])},n.prototype.refreshUser=function(){if(null!==JSON.parse(sessionStorage.getItem("user"))&&(this.user=JSON.parse(sessionStorage.getItem("user"))),null!==JSON.parse(sessionStorage.getItem("profilePicture"))){var n=JSON.parse(sessionStorage.getItem("profilePicture"));this.profileImg="data:"+n.file_type+";base64,"+n.file}return!0},n}(),b=e.pb({encapsulation:0,styles:[["[_nghost-%COMP%]   .navbar[_ngcontent-%COMP%]{background-color:#343a40}[_nghost-%COMP%]   .navbar[_ngcontent-%COMP%]   .navbar-brand[_ngcontent-%COMP%]{color:#fff}[_nghost-%COMP%]   .navbar[_ngcontent-%COMP%]   .nav-item[_ngcontent-%COMP%] > a[_ngcontent-%COMP%]{color:#999}[_nghost-%COMP%]   .navbar[_ngcontent-%COMP%]   .nav-item[_ngcontent-%COMP%] > a[_ngcontent-%COMP%]:hover{color:#fff}"]],data:{}});function d(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,1,"small",[],null,null,null,null,null)),(n()(),e.Jb(1,null,["",""]))],null,function(n,l){n(l,1,0,l.component.user.name)})}function g(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,32,"nav",[["class","navbar navbar-expand-lg fixed-top bg-dark"]],null,null,null,null,null)),(n()(),e.rb(1,0,null,null,2,"div",[["class","navbar-brand"]],null,null,null,null,null)),(n()(),e.rb(2,0,null,null,0,"img",[["alt",""],["height","30"],["src","assets/images/logo.png"],["width","auto"]],null,null,null,null,null)),(n()(),e.rb(3,0,null,null,0,"span",[["class","ml-2 text-light"]],null,null,null,null,null)),(n()(),e.rb(4,0,null,null,1,"button",[["class","navbar-toggler"],["type","button"]],null,[[null,"click"]],function(n,l,t){var e=!0;return"click"===l&&(e=!1!==n.component.toggleSidebar()&&e),e},null,null)),(n()(),e.rb(5,0,null,null,0,"i",[["aria-hidden","true"],["class","fa fa-bars text-muted"]],null,null,null,null,null)),(n()(),e.rb(6,0,null,null,26,"div",[["class","collapse navbar-collapse"]],null,null,null,null,null)),(n()(),e.rb(7,0,null,null,25,"ul",[["class","navbar-nav ml-auto"]],null,null,null,null,null)),(n()(),e.rb(8,0,null,null,24,"li",[["class","nav-item dropdown"],["ngbDropdown",""]],[[2,"show",null]],null,null,null,null)),e.qb(9,212992,null,2,r.r,[e.h,r.s,i.c,e.A],null,null),e.Hb(335544320,1,{_menu:0}),e.Hb(335544320,2,{_anchor:0}),(n()(),e.rb(12,0,null,null,8,"a",[["aria-haspopup","true"],["class","nav-link text-light dropdown-toggle"],["href","javascript:void(0)"],["ngbDropdownToggle",""]],[[1,"aria-expanded",0]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,13).toggleOpen()&&o),o},null,null)),e.qb(13,16384,null,0,r.ab,[r.r,e.k],null,null),e.Gb(2048,[[2,4]],r.Z,null,[r.ab]),(n()(),e.rb(15,0,null,null,1,"span",[],null,null,null,null,null)),(n()(),e.rb(16,0,null,null,0,"img",[["class","rounded-circle"],["height","32px"],["width","32px"]],[[8,"src",4]],null,null,null,null)),(n()(),e.Jb(-1,null,["\xa0"])),(n()(),e.ib(16777216,null,null,1,null,d)),e.qb(19,16384,null,0,i.l,[e.Q,e.N],{ngIf:[0,"ngIf"]},null),(n()(),e.rb(20,0,null,null,0,"b",[["class","caret"]],null,null,null,null,null)),(n()(),e.rb(21,0,null,null,11,"div",[["class","dropdown-menu-right"],["ngbDropdownMenu",""]],[[2,"dropdown-menu",null],[2,"show",null],[1,"x-placement",0]],null,null,null,null)),e.qb(22,16384,[[1,4]],0,r.Y,[r.r,e.k,e.F],null,null),(n()(),e.rb(23,0,null,null,4,"a",[["class","dropdown-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,24).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),o},null,null)),e.qb(24,671744,null,0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(25,1),(n()(),e.rb(26,0,null,null,0,"i",[["class","fa fa-fw fa-user"]],null,null,null,null,null)),(n()(),e.Jb(-1,null,[" Perfil "])),(n()(),e.rb(28,0,null,null,4,"a",[["class","dropdown-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0,u=n.component;return"click"===l&&(o=!1!==e.Bb(n,29).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),"click"===l&&(o=!1!==u.logout()&&o),o},null,null)),e.qb(29,671744,null,0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(30,1),(n()(),e.rb(31,0,null,null,0,"i",[["class","fa fa-fw fa-power-off"]],null,null,null,null,null)),(n()(),e.Jb(-1,null,[" Cerrar Sesi\xf3n "]))],function(n,l){var t=l.component;n(l,9,0),n(l,19,0,t.refreshUser());var e=n(l,25,0,"/profile");n(l,24,0,e);var o=n(l,30,0,"/login");n(l,29,0,o)},function(n,l){var t=l.component;n(l,8,0,e.Bb(l,9).isOpen()),n(l,12,0,e.Bb(l,13).dropdown.isOpen()),n(l,16,0,e.tb(1,"",t.profileImg,"")),n(l,21,0,!0,e.Bb(l,22).dropdown.isOpen(),e.Bb(l,22).placement),n(l,23,0,e.Bb(l,24).target,e.Bb(l,24).href),n(l,28,0,e.Bb(l,29).target,e.Bb(l,29).href)})}var p=function(){function n(n){var l=this;this.router=n,this.profileImg="assets/images/accounts.png",this.collapsedEvent=new e.m,this.router.events.subscribe(function(n){n instanceof a.d&&window.innerWidth<=992&&l.isToggled()&&l.toggleSidebar()})}return n.prototype.ngOnInit=function(){this.isActive=!1,this.collapsed=!1,this.showMenu="",this.pushRightClass="push-right"},n.prototype.eventCalled=function(){this.isActive=!this.isActive},n.prototype.addExpandClass=function(n){this.showMenu=n===this.showMenu?"0":n},n.prototype.isToggled=function(){return document.querySelector("body").classList.contains(this.pushRightClass)},n.prototype.toggleSidebar=function(){document.querySelector("body").classList.toggle(this.pushRightClass)},n.prototype.logOut=function(){sessionStorage.clear(),this.router.navigate(["/login"])},n.prototype.refreshUser=function(){if(null!==JSON.parse(sessionStorage.getItem("user"))&&(this.user=JSON.parse(sessionStorage.getItem("user"))),null!==JSON.parse(sessionStorage.getItem("profilePicture"))){var n=JSON.parse(sessionStorage.getItem("profilePicture"));this.profileImg="data:"+n.file_type+";base64,"+n.file}return!0},n}(),f=e.pb({encapsulation:0,styles:[[".sidebar[_ngcontent-%COMP%]{border-radius:0;position:fixed;z-index:1000;top:59px;left:235px;width:235px;margin-left:-235px;margin-bottom:0;border:none;overflow-y:auto;background-color:#343a40;bottom:0;overflow-x:hidden;padding-bottom:40px;white-space:nowrap;transition:all .2s ease-in-out}.sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   a.list-group-item[_ngcontent-%COMP%]{background:#343a40;border:0;border-radius:0;color:#999;text-decoration:none}.sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   a.list-group-item[_ngcontent-%COMP%]   .fa[_ngcontent-%COMP%]{margin-right:10px}.sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   a.router-link-active[_ngcontent-%COMP%], .sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{background:#292d32;color:#fff}.sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   .header-fields[_ngcontent-%COMP%]{padding-top:10px}.sidebar[_ngcontent-%COMP%]   .list-group[_ngcontent-%COMP%]   .header-fields[_ngcontent-%COMP%] > .list-group-item[_ngcontent-%COMP%]:first-child{border-top:1px solid rgba(255,255,255,.2)}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   [_ngcontent-%COMP%]:focus{border-radius:none;border:none}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]{font-size:1rem;height:50px;margin-bottom:0}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{color:#999;text-decoration:none;font-weight:400;background:#343a40}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]   span[_ngcontent-%COMP%]{position:relative;display:block;padding:1rem 1.5rem .75rem}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:focus, .sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{color:#fff;outline:0;outline-offset:-2px}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-title[_ngcontent-%COMP%]:hover{background:#292d32}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-collapse[_ngcontent-%COMP%]{border-radious:0;border:none}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-collapse[_ngcontent-%COMP%]   .panel-body[_ngcontent-%COMP%]   .list-group-item[_ngcontent-%COMP%]{border-radius:0;background-color:#343a40;border:0 solid transparent}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-collapse[_ngcontent-%COMP%]   .panel-body[_ngcontent-%COMP%]   .list-group-item[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{color:#999}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-collapse[_ngcontent-%COMP%]   .panel-body[_ngcontent-%COMP%]   .list-group-item[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{color:#fff}.sidebar[_ngcontent-%COMP%]   .sidebar-dropdown[_ngcontent-%COMP%]   .panel-collapse[_ngcontent-%COMP%]   .panel-body[_ngcontent-%COMP%]   .list-group-item[_ngcontent-%COMP%]:hover{background:#292d32}.nested-menu[_ngcontent-%COMP%]   .list-group-item[_ngcontent-%COMP%]{cursor:pointer}.nested-menu[_ngcontent-%COMP%]   .nested[_ngcontent-%COMP%]{list-style-type:none}.nested-menu[_ngcontent-%COMP%]   ul.submenu[_ngcontent-%COMP%]{display:none;height:0}.nested-menu[_ngcontent-%COMP%]   .expand[_ngcontent-%COMP%]   ul.submenu[_ngcontent-%COMP%]{display:block;list-style-type:none;height:auto}.nested-menu[_ngcontent-%COMP%]   .expand[_ngcontent-%COMP%]   ul.submenu[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{color:#fff;padding:10px;display:block}@media screen and (max-width:992px){.sidebar[_ngcontent-%COMP%]{top:60px;left:0}}@media print{.sidebar[_ngcontent-%COMP%]{display:none!important}}@media (min-width:992px){.header-fields[_ngcontent-%COMP%]{display:none}}[_ngcontent-%COMP%]::-webkit-scrollbar{width:8px}[_ngcontent-%COMP%]::-webkit-scrollbar-track{-webkit-box-shadow:inset 0 0 0 #fff;border-radius:3px}[_ngcontent-%COMP%]::-webkit-scrollbar-thumb{border-radius:3px;-webkit-box-shadow:inset 0 0 3px #fff}.toggle-button[_ngcontent-%COMP%]{position:fixed;width:236px;cursor:pointer;padding:12px;bottom:0;color:#999;background:#212529;border-top:1px solid #999;transition:all .2s ease-in-out}.toggle-button[_ngcontent-%COMP%]   i[_ngcontent-%COMP%]{font-size:23px}.toggle-button[_ngcontent-%COMP%]:hover{background:#292d32;color:#fff}.collapsed[_ngcontent-%COMP%]{width:50px}.collapsed[_ngcontent-%COMP%]   span[_ngcontent-%COMP%]{display:none}"]],data:{}});function h(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,1,"small",[],null,null,null,null,null)),(n()(),e.Jb(1,null,["",""]))],null,function(n,l){n(l,1,0,l.component.user.name)})}function C(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,62,"nav",[["class","sidebar"]],null,null,null,null,null)),e.qb(1,278528,null,0,i.j,[e.t,e.u,e.k,e.F],{klass:[0,"klass"],ngClass:[1,"ngClass"]},null),e.Eb(2,{sidebarPushRight:0,collapsed:1}),(n()(),e.rb(3,0,null,null,59,"div",[["class","list-group"]],null,null,null,null,null)),(n()(),e.rb(4,0,null,null,10,"a",[["class","list-group-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,5).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),o},null,null)),e.qb(5,671744,[[2,4]],0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(6,1),e.qb(7,1720320,null,2,a.m,[a.l,e.k,e.F,e.h],{routerLinkActive:[0,"routerLinkActive"]},null),e.Hb(603979776,1,{links:1}),e.Hb(603979776,2,{linksWithHrefs:1}),e.Cb(10,1),(n()(),e.rb(11,0,null,null,0,"i",[["class","fas fa-tachometer-alt"]],null,null,null,null,null)),(n()(),e.Jb(-1,null,["\xa0 "])),(n()(),e.rb(13,0,null,null,1,"span",[],null,null,null,null,null)),(n()(),e.Jb(-1,null,["Main"])),(n()(),e.rb(15,0,null,null,23,"div",[["class","nested-menu"]],null,null,null,null,null)),(n()(),e.rb(16,0,null,null,2,"a",[["class","list-group-item"]],null,[[null,"click"]],function(n,l,t){var e=!0;return"click"===l&&(e=!1!==n.component.addExpandClass("bdd lschess")&&e),e},null,null)),(n()(),e.rb(17,0,null,null,0,"span",[["class","fas fa-database"]],null,null,null,null,null)),(n()(),e.Jb(-1,null,["\xa0BDD LSCHESS "])),(n()(),e.rb(19,0,null,null,19,"li",[["class","nested"]],[[2,"expand",null]],null,null,null,null)),(n()(),e.rb(20,0,null,null,18,"ul",[["class","submenu"]],null,null,null,null,null)),(n()(),e.rb(21,0,null,null,8,"li",[],null,null,null,null,null)),(n()(),e.rb(22,0,null,null,7,"a",[["class","list-group-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,23).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),o},null,null)),e.qb(23,671744,[[4,4]],0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(24,1),e.qb(25,1720320,null,2,a.m,[a.l,e.k,e.F,e.h],{routerLinkActive:[0,"routerLinkActive"]},null),e.Hb(603979776,3,{links:1}),e.Hb(603979776,4,{linksWithHrefs:1}),e.Cb(28,1),(n()(),e.Jb(-1,null,["Game "])),(n()(),e.rb(30,0,null,null,8,"li",[],null,null,null,null,null)),(n()(),e.rb(31,0,null,null,7,"a",[["class","list-group-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,32).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),o},null,null)),e.qb(32,671744,[[6,4]],0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(33,1),e.qb(34,1720320,null,2,a.m,[a.l,e.k,e.F,e.h],{routerLinkActive:[0,"routerLinkActive"]},null),e.Hb(603979776,5,{links:1}),e.Hb(603979776,6,{linksWithHrefs:1}),e.Cb(37,1),(n()(),e.Jb(-1,null,["BestMove "])),(n()(),e.rb(39,0,null,null,23,"div",[["class","nested-menu"]],null,null,null,null,null)),(n()(),e.rb(40,0,null,null,5,"a",[["class","list-group-item"]],null,[[null,"click"]],function(n,l,t){var e=!0;return"click"===l&&(e=!1!==n.component.addExpandClass("profile")&&e),e},null,null)),(n()(),e.rb(41,0,null,null,1,"span",[],null,null,null,null,null)),(n()(),e.rb(42,0,null,null,0,"img",[["class","rounded-circle"],["height","32px"],["width","32px"]],[[8,"src",4]],null,null,null,null)),(n()(),e.Jb(-1,null,["\xa0"])),(n()(),e.ib(16777216,null,null,1,null,h)),e.qb(45,16384,null,0,i.l,[e.Q,e.N],{ngIf:[0,"ngIf"]},null),(n()(),e.rb(46,0,null,null,16,"li",[["class","nested"]],[[2,"expand",null]],null,null,null,null)),(n()(),e.rb(47,0,null,null,15,"ul",[["class","submenu"]],null,null,null,null,null)),(n()(),e.rb(48,0,null,null,8,"li",[],null,null,null,null,null)),(n()(),e.rb(49,0,null,null,7,"a",[["class","list-group-item"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0;return"click"===l&&(o=!1!==e.Bb(n,50).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),o},null,null)),e.qb(50,671744,[[8,4]],0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(51,1),e.qb(52,1720320,null,2,a.m,[a.l,e.k,e.F,e.h],{routerLinkActive:[0,"routerLinkActive"]},null),e.Hb(603979776,7,{links:1}),e.Hb(603979776,8,{linksWithHrefs:1}),e.Cb(55,1),(n()(),e.Jb(-1,null,["Perfil "])),(n()(),e.rb(57,0,null,null,5,"li",[],null,null,null,null,null)),(n()(),e.rb(58,0,null,null,4,"a",[],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var o=!0,u=n.component;return"click"===l&&(o=!1!==e.Bb(n,59).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&o),"click"===l&&(o=!1!==u.logOut()&&o),o},null,null)),e.qb(59,671744,null,0,a.n,[a.l,a.a,i.i],{routerLink:[0,"routerLink"]},null),e.Cb(60,1),(n()(),e.rb(61,0,null,null,1,"span",[],null,null,null,null,null)),(n()(),e.Jb(-1,null,["\xa0Cerrar Sesi\xf3n"]))],function(n,l){var t=l.component,e=n(l,2,0,t.isActive,t.collapsed);n(l,1,0,"sidebar",e);var o=n(l,6,0,"/main");n(l,5,0,o);var u=n(l,10,0,"router-link-active");n(l,7,0,u);var r=n(l,24,0,"/game");n(l,23,0,r);var i=n(l,28,0,"router-link-active");n(l,25,0,i);var a=n(l,33,0,"/best_move");n(l,32,0,a);var s=n(l,37,0,"router-link-active");n(l,34,0,s),n(l,45,0,t.refreshUser());var c=n(l,51,0,"/profile");n(l,50,0,c);var b=n(l,55,0,"router-link-active");n(l,52,0,b);var d=n(l,60,0,"/login");n(l,59,0,d)},function(n,l){var t=l.component;n(l,4,0,e.Bb(l,5).target,e.Bb(l,5).href),n(l,19,0,"bdd lschess"===t.showMenu),n(l,22,0,e.Bb(l,23).target,e.Bb(l,23).href),n(l,31,0,e.Bb(l,32).target,e.Bb(l,32).href),n(l,42,0,e.tb(1,"",t.profileImg,"")),n(l,46,0,"profile"===t.showMenu),n(l,49,0,e.Bb(l,50).target,e.Bb(l,50).href),n(l,58,0,e.Bb(l,59).target,e.Bb(l,59).href)})}var m=t("S2dX"),M=function(){function n(n){this.profilePictureDataService=n}return n.prototype.ngOnInit=function(){this.getProfilePicture()},n.prototype.getProfilePicture=function(){this.profilePictureDataService.get().then(function(n){void 0!==n&&void 0===n.error&&sessionStorage.setItem("profilePicture",JSON.stringify(n))}).catch(function(n){})},n.prototype.receiveCollapsed=function(n){this.collapedSideBar=n},n}(),O=e.pb({encapsulation:0,styles:[["*[_ngcontent-%COMP%]{transition:margin-left .2s ease-in-out}.main-container[_ngcontent-%COMP%]{margin-top:56px;margin-left:235px;padding:15px;-ms-overflow-x:hidden;overflow-x:hidden;overflow-y:scroll;position:relative;overflow:hidden}.collapsed[_ngcontent-%COMP%]{margin-left:100px}@media screen and (max-width:992px){.main-container[_ngcontent-%COMP%]{margin-left:0!important}}@media print{.main-container[_ngcontent-%COMP%]{margin-top:0!important;margin-left:0!important}}"]],data:{}});function P(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,1,"app-navbar",[],null,null,null,g,b)),e.qb(1,114688,null,0,c,[a.l],null,null),(n()(),e.rb(2,0,null,null,1,"app-sidebar",[],null,[[null,"collapsedEvent"]],function(n,l,t){var e=!0;return"collapsedEvent"===l&&(e=!1!==n.component.receiveCollapsed(t)&&e),e},C,f)),e.qb(3,114688,null,0,p,[a.l],null,{collapsedEvent:"collapsedEvent"}),(n()(),e.rb(4,0,null,null,4,"section",[["class","main-container"]],null,null,null,null,null)),e.qb(5,278528,null,0,i.j,[e.t,e.u,e.k,e.F],{klass:[0,"klass"],ngClass:[1,"ngClass"]},null),e.Eb(6,{collapsed:0}),(n()(),e.rb(7,16777216,null,null,1,"router-outlet",[],null,null,null,null,null)),e.qb(8,212992,null,0,a.p,[a.b,e.Q,e.j,[8,null],e.h],null,null)],function(n,l){var t=l.component;n(l,1,0),n(l,3,0);var e=n(l,6,0,t.collapedSideBar);n(l,5,0,"main-container",e),n(l,8,0)},null)}function _(n){return e.Lb(0,[(n()(),e.rb(0,0,null,null,1,"app-layout",[],null,null,null,P,O)),e.qb(1,114688,null,0,M,[m.a],null,null)],function(n,l){n(l,1,0)},null)}var v=e.nb("app-layout",M,_,{},{},[]),k=t("sE5F"),y=function(){return function(){}}();t.d(l,"LayoutModuleNgFactory",function(){return w});var w=e.ob(o,[],function(n){return e.yb([e.zb(512,e.j,e.db,[[8,[u.a,v]],[3,e.j],e.y]),e.zb(4608,i.n,i.m,[e.v,[2,i.C]]),e.zb(4608,k.c,k.c,[]),e.zb(4608,k.h,k.b,[]),e.zb(5120,k.j,k.k,[]),e.zb(4608,k.i,k.i,[k.c,k.h,k.j]),e.zb(4608,k.g,k.a,[]),e.zb(5120,k.e,k.l,[k.i,k.g]),e.zb(4608,m.a,m.a,[k.e]),e.zb(1073742336,i.b,i.b,[]),e.zb(1073742336,a.o,a.o,[[2,a.u],[2,a.l]]),e.zb(1073742336,y,y,[]),e.zb(1073742336,r.t,r.t,[]),e.zb(1073742336,k.f,k.f,[]),e.zb(1073742336,o,o,[]),e.zb(1024,a.j,function(){return[[{path:"",component:M,children:[{path:"",redirectTo:"main"},{path:"main",loadChildren:"./main/main.module#MainModule"},{path:"profile",loadChildren:"./profile/profile.module#ProfileModule"},{path:"blank",loadChildren:"./blank-page/blank-page.module#BlankPageModule"},{path:"not-found",loadChildren:"./not-found/not-found.module#NotFoundModule"},{path:"game",loadChildren:"./CRUD/LSCHESS/Game/game.module#GameModule"},{path:"best_move",loadChildren:"./CRUD/LSCHESS/BestMove/bestmove.module#BestMoveModule"},{path:"**",redirectTo:"not-found"}]}]]},[])])})}}]);