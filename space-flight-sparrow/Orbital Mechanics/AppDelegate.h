//
//  AppDelegate.h
//  Orbital Mechanics
//
//  Created by Michael Stratford on 9/13/12.
//  Copyright (c) 2012 Michael Stratford. All rights reserved.
//

#import <UIKit/UIKit.h>

@class MainMenuViewController;
@class Reachability;

@interface AppDelegate : UIResponder <UIApplicationDelegate>
{
    Reachability* internetReachable;
    Reachability* hostReachable;
    bool internetActive;
    bool hostActive;
}

@property (strong, nonatomic) UIWindow *window;
@property (strong, nonatomic) Reachability *internetReachable;
@property (strong, nonatomic) Reachability *hostReachable;

@property (nonatomic) bool internetActive;
@property (nonatomic) bool hostActive;

@property (strong, nonatomic) MainMenuViewController *mainMenuViewController;
-(void) checkNetworkStatus:(NSNotification *)notice;

@end
