//
//  Do_We_Have_EnoughAppDelegate.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright JLOOP 2009. All rights reserved.
//

@interface Do_We_Have_EnoughAppDelegate : NSObject <UIApplicationDelegate> {
    
    UIWindow *window;
    UINavigationController *navigationController;
	NSString *launchEventID;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) IBOutlet UINavigationController *navigationController;
@property (nonatomic, retain) IBOutlet NSString *launchEventID;

@end

