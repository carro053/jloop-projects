//
//  BlackMagicAppDelegate.m
//  BlackMagic
//
//  Created by Michael Stratford on 12/03/2010.
//

#import "BlackMagicAppDelegate.h"
#import "BlackMagicViewController.h"
#import "SettingsTracker.h"

@implementation BlackMagicAppDelegate

@synthesize window;
@synthesize blackMagicViewController;

#pragma mark -
#pragma mark Application lifecycle

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {
    [self.window addSubview:blackMagicViewController.view];
    SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if([settings.overlayStartsOn intValue])
	{
		[blackMagicViewController overlayOn];
	}else{
		[blackMagicViewController overlayOff];
	}
	[settings release];
    [window makeKeyAndVisible];
	
	return YES;
}


- (void)applicationWillResignActive:(UIApplication *)application {
    [[NSNotificationCenter defaultCenter] postNotificationName:@"appClosing" object:nil];
	[blackMagicViewController stopCameraCapture];
	[blackMagicViewController overlayOff];
}


- (void)applicationDidEnterBackground:(UIApplication *)application {
}


- (void)applicationWillEnterForeground:(UIApplication *)application {
	/*SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if([settings.overlayStartsOn intValue])
	{
		[blackMagicViewController overlayOn];
	}else{
		[blackMagicViewController overlayOff];
	}
	[settings release];*/
    /*
     Called as part of  transition from the background to the inactive state: here you can undo many of the changes made on entering the background.
     */
}


- (void)applicationDidBecomeActive:(UIApplication *)application {
	[blackMagicViewController startCameraCapture];
    /*
     Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
     */
}


- (void)applicationWillTerminate:(UIApplication *)application {
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings saveColorMode:[NSString stringWithFormat: @"%d", blackMagicViewController.currentCM]];
	[settings release];
}


#pragma mark -
#pragma mark Memory management

- (void)applicationDidReceiveMemoryWarning:(UIApplication *)application {
    /*
     Free up as much memory as possible by purging cached data objects that can be recreated (or reloaded from disk) later.
     */
}


- (void)dealloc {
	[blackMagicViewController release];
    [window release];
    [super dealloc];
}


@end
