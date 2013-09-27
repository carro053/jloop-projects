//
//  Do_We_Have_EnoughAppDelegate.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright JLOOP 2009. All rights reserved.
//
#import "HomeController.h"
#import "EventDetailsViewController.h"
#import "EventMemberListViewController.h"

@interface Do_We_Have_EnoughAppDelegate : NSObject <UIApplicationDelegate> {
    HomeController *homeController;
    UIWindow *window;
    UINavigationController *navigationController;
	NSString *launchEventID;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) IBOutlet UINavigationController *navigationController;
@property (nonatomic, retain) IBOutlet NSString *launchEventID;
@property (nonatomic, retain) IBOutlet HomeController *homeController;

@property (nonatomic, retain) EventDetailsViewController *eventDetailsViewController;
@property (nonatomic, retain) EventMemberListViewController *eventMemberListViewController;

@end

