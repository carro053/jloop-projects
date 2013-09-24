//
//  Do_We_Have_EnoughAppDelegate.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright JLOOP 2009. All rights reserved.
//

#import "Do_We_Have_EnoughAppDelegate.h"
#import "RootViewController.h"
#import "HomeController.h"
#import "SettingsTracker.h"
#import "TestFlight.h"
#import "FlurryAnalytics.h"
#import "EventDetailsViewController.h"
//#import "Beacon.h"


@implementation Do_We_Have_EnoughAppDelegate
@synthesize homeController;
@synthesize window;
@synthesize navigationController;
@synthesize launchEventID;


#pragma mark -
#pragma mark Application lifecycle

- (void)application:(UIApplication *)app didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)deviceToken {          
	NSLog(@"devToken=%@",deviceToken);
	NSString *dt=[[deviceToken description] stringByTrimmingCharactersInSet:[NSCharacterSet characterSetWithCharactersInString:@"<>"]];
	//UIDevice *device = [UIDevice currentDevice];
	//NSString *uniqueIdentifier = [device uniqueIdentifier];
    
    NSString *uniqueIdentifier = [UIDevice currentDevice].identifierForVendor.UUIDString;
    
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if ([settings.emailAddress isEqualToString:@"false"]) {
		//do nothing
	} else {
		NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@&device_token=%@", settings.emailAddress, uniqueIdentifier, dt];
		//NSLog(postString);
		
		NSString *URL = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/save_token.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
		NSURL *url = [NSURL URLWithString:URL];
		NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:url];
		NSString *msgLength = [NSString stringWithFormat:@"%d", [postString length]];
		
		[req addValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
		[req addValue:msgLength forHTTPHeaderField:@"Content-Length"];
		[req setHTTPMethod:@"POST"];
		[req setHTTPBody: [postString dataUsingEncoding:NSUTF8StringEncoding]];
		
		NSURLConnection *conn = [[NSURLConnection alloc] initWithRequest:req delegate:nil];
		[conn release];
		[URL release];
	}
	[settings release];
}
- (void)application:(UIApplication *)app didFailToRegisterForRemoteNotificationsWithError:(NSError *)err {     
    NSLog(@"Error in registration. Error: %@", err);
}

- (void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo
{
	NSLog(@"got user info: %@", userInfo);
    self.launchEventID = [[userInfo objectForKey:@"push_data"] objectForKey:@"event_id"];
    UIApplicationState state = [application applicationState];
    NSLog(@"TESTNOTIFICATIONS");
    if (state == UIApplicationStateActive) {
        NSDictionary *aps = [userInfo objectForKey:@"aps"];
        NSString *myAlert = [aps objectForKey:@"alert"];
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"DoWeHaveEnough" message:myAlert delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
        [alert release];
        if ([homeController respondsToSelector:@selector(checkValidation)])
            [homeController performSelector:@selector(checkValidation)];
    }else{
        
        [[UIApplication sharedApplication] setApplicationIconBadgeNumber: 1];
        [[UIApplication sharedApplication] setApplicationIconBadgeNumber: 0];
        [[UIApplication sharedApplication] cancelAllLocalNotifications];
    }
}
- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    //reset validation for first run on iOS 6+
    NSString *filename = [[NSString alloc] initWithString:@"vendorIdFirstRunCheck"];
    NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	NSString *dataFilePath = [documentsDirectory stringByAppendingPathComponent:filename];
    BOOL fileExists = [[NSFileManager defaultManager] fileExistsAtPath:dataFilePath];
    if(!fileExists) {
        //alert reset validation and tell the user all about it
        SettingsTracker *settings = [[SettingsTracker alloc] init];
        [settings resetData];
        [settings release];
        
        NSString *content = @"User ran the app for the first time";
        NSData *fileContents = [content dataUsingEncoding:NSUTF8StringEncoding];
        [[NSFileManager defaultManager] createFileAtPath:dataFilePath
                                                contents:fileContents
                                              attributes:nil];
    }
    
    self.window.frame = CGRectMake(0, 0, [[UIScreen mainScreen]bounds].size.width, [[UIScreen mainScreen]bounds].size.height);
    [TestFlight takeOff:@"4daac12df9d7ba3264eef02799dfd0bf_NDk0MjIwMTEtMTAtMDUgMTI6MjI6MjkuMjc5Nzgw"];
	[window addSubview:[navigationController view]];
    [window makeKeyAndVisible];
	NSLog(@"got launchoptions: %@", launchOptions);
	NSDictionary *push_data = [[launchOptions objectForKey:@"UIApplicationLaunchOptionsRemoteNotificationKey"] objectForKey:@"push_data"];
	NSString *myEventID = [push_data objectForKey:@"event_id"];
    if([myEventID intValue] > 0)
    {
        [[UIApplication sharedApplication] setApplicationIconBadgeNumber: 1];
        [[UIApplication sharedApplication] setApplicationIconBadgeNumber: 0];
        [[UIApplication sharedApplication] cancelAllLocalNotifications];
        self.launchEventID = myEventID;
    }else{
        self.launchEventID = @"";
    }
	[FlurryAnalytics startSession:@"7994fb80cece2b2663b0c3188280ae63"];
	
	[[UIApplication sharedApplication] registerForRemoteNotificationTypes:(UIRemoteNotificationTypeBadge | UIRemoteNotificationTypeSound |UIRemoteNotificationTypeAlert)];
    
	return YES;
}

- (void)applicationWillTerminate:(UIApplication *)application {
}

- (void)applicationDidBecomeActive:(UIApplication *)application {
    if([self.launchEventID intValue] > 0)
    {
        if ([homeController respondsToSelector:@selector(checkValidation)]){
            [homeController performSelector:@selector(checkValidation)];
            
        }
    }
    if ([navigationController.topViewController respondsToSelector:@selector(refreshData)])
        [navigationController.topViewController performSelector:@selector(refreshData)];
}


#pragma mark -
#pragma mark Memory management

- (void)dealloc {
	[navigationController release];
	[window release];
	[super dealloc];
}


@end

