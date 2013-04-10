//
//  AppDelegate.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import "cocos2d.h"

#import "AppDelegate.h"
#import "GameConfig.h"
#import "TestFlight.h"
#import "UIDevice+IdentifierAddition.h"
#import "MainMenuViewController.h"
#import <GameKit/GameKit.h>

/*
#import "HelloWorldLayer.h"
#import "RootViewController.h"
 */

@implementation AppDelegate

@synthesize window;

- (void) removeStartupFlicker
{
	//
	// THIS CODE REMOVES THE STARTUP FLICKER
	//
	// Uncomment the following code if you Application only supports landscape mode
	//
#if GAME_AUTOROTATION == kGameAutorotationUIViewController

//	CC_ENABLE_DEFAULT_GL_STATES();
//	CCDirector *director = [CCDirector sharedDirector];
//	CGSize size = [director winSize];
//	CCSprite *sprite = [CCSprite spriteWithFile:@"Default.png"];
//	sprite.position = ccp(size.width/2, size.height/2);
//	sprite.rotation = -90;
//	[sprite visit];
//	[[director openGLView] swapBuffers];
//	CC_ENABLE_DEFAULT_GL_STATES();
	
#endif // GAME_AUTOROTATION == kGameAutorotationUIViewController	
}
- (void) applicationDidFinishLaunching:(UIApplication*)application
{
    [[UIApplication sharedApplication] registerForRemoteNotificationTypes: (UIRemoteNotificationTypeAlert | UIRemoteNotificationTypeBadge | UIRemoteNotificationTypeSound)];
    [TestFlight takeOff:@"4daac12df9d7ba3264eef02799dfd0bf_NDk0MjIwMTEtMTAtMDUgMTI6MjI6MjkuMjc5Nzgw"];
#define TESTING 1
#ifdef TESTING
    NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
    [TestFlight setDeviceIdentifier:deviceUDID];
#endif
	// Init the window
	//window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    
    
    //Game Center auth
    GKLocalPlayer *localPlayer = [GKLocalPlayer localPlayer];
    [localPlayer authenticateWithCompletionHandler:^(NSError *error) {
        if (localPlayer.isAuthenticated)
        {
            // Player was successfully authenticated.
            // Perform additional tasks for the authenticated player.
            NSLog(@"Game Center auth succeeded");
        } else {
            NSLog(@"Game Center auth failed");
        }
    }];
	
	// Try to use CADisplayLink director
	// if it fails (SDK < 3.1) use the default director
	if( ! [CCDirector setDirectorType:kCCDirectorTypeDisplayLink] )
		[CCDirector setDirectorType:kCCDirectorTypeDefault];
	
	
	CCDirector *director = [CCDirector sharedDirector];
	/*
	// Init the View Controller
	viewController = [[RootViewController alloc] initWithNibName:nil bundle:nil];
	viewController.wantsFullScreenLayout = YES;
	
	//
	// Create the EAGLView manually
	//  1. Create a RGB565 format. Alternative: RGBA8
	//	2. depth format of 0 bit. Use 16 or 24 bit for 3d effects, like CCPageTurnTransition
	//
	//
	EAGLView *glView = [EAGLView viewWithFrame:[window bounds]
								   pixelFormat:kEAGLColorFormatRGB565	// kEAGLColorFormatRGBA8
								   depthFormat:0						// GL_DEPTH_COMPONENT16_OES
						];
	
	// attach the openglView to the director
	[director setOpenGLView:glView];
    */
	
    // Enables High Res mode (Retina Display) on iPhone 4 and maintains low res on all other devices
    //if( ! [director enableRetinaDisplay:YES] )
    //    CCLOG(@"Retina Display Not supported");
	
	//
	// VERY IMPORTANT:
	// If the rotation is going to be controlled by a UIViewController
	// then the device orientation should be "Portrait".
	//
	// IMPORTANT:
	// By default, this template only supports Landscape orientations.
	// Edit the RootViewController.m file to edit the supported orientations.
	//
#if GAME_AUTOROTATION == kGameAutorotationUIViewController
	[director setDeviceOrientation:kCCDeviceOrientationPortrait];
#else
	[director setDeviceOrientation:kCCDeviceOrientationLandscapeLeft];
#endif
	
	[director setAnimationInterval:1.0/60];
	[director setDisplayFPS:NO];
	
	/*
	// make the OpenGLView a child of the view controller
	[viewController setView:glView];
	
	// make the View Controller a child of the main window
	[window addSubview: viewController.view];
     */
    
    //window = [[UIWindow alloc] initWithFrame:[[UIScreen mainScreen] bounds]];
    //window.frame = CGRectMake(0, 0, [director winSize].width, [director winSize].height);
	[window makeKeyAndVisible];
	
	// Default texture format for PNG/BMP/TIFF/JPEG/GIF images
	// It can be RGBA8888, RGBA4444, RGB5_A1, RGB565
	// You can change anytime.
	[CCTexture2D setDefaultAlphaPixelFormat:kCCTexture2DPixelFormat_RGBA8888];

	
	// Removes the startup flicker
	[self removeStartupFlicker];
	
	
    // Run the intro Scene
	//[[CCDirector sharedDirector] runWithScene: [BlankScene scene]];
    
}
- (BOOL)application:(UIApplication *)application handleOpenURL:(NSURL *)url
{
    if([url.host isEqualToString:@"viewSolution"])
    {
        int mission_id = [[url.pathComponents objectAtIndex:1] intValue];
        int solution_id = [[url.pathComponents objectAtIndex:2] intValue];
        if(mission_id > 0 && solution_id > 0)
        {
            [TestFlight passCheckpoint:@"Viewing Solution from Web Link"];
            ViewMissionSolutionViewController *viewMissionSolutionViewController = [[ViewMissionSolutionViewController alloc] initWithNibName:@"ViewMissionSolutionViewController" bundle:nil withMissionId:mission_id andSolutionId:solution_id];
            [self.window.rootViewController dismissModalViewControllerAnimated:NO];
            [self.window.rootViewController presentModalViewController:viewMissionSolutionViewController animated:NO];
            [viewMissionSolutionViewController release];
        }
    }
    return YES;
}
- (void)application:(UIApplication*)application didRegisterForRemoteNotificationsWithDeviceToken:(NSData*)deviceToken
{
    NSString *tokenStr = [deviceToken description];
    NSString *pushToken = [[[[tokenStr 
                              stringByReplacingOccurrencesOfString:@"<" withString:@""] 
                             stringByReplacingOccurrencesOfString:@">" withString:@""] 
                            stringByReplacingOccurrencesOfString:@" " withString:@""] retain];
    
    // Save the token to server
    NSString *urlStr = [NSString stringWithFormat:@"http://dev.gravitationsapp.com/puzzles/savePushToken"];
    NSURL *url = [NSURL URLWithString:urlStr];
    NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:url];
    
    [req setHTTPMethod:@"POST"];
    [req setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-type"];
    NSMutableData *postBody = [NSMutableData data];
    NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
    [postBody appendData:[[NSString stringWithFormat:@"device_id=%@", deviceUDID] 
                          dataUsingEncoding:NSUTF8StringEncoding]];
    [postBody appendData:[[NSString stringWithFormat:@"&token=%@", 
                           pushToken] dataUsingEncoding:NSUTF8StringEncoding]];
    
    [req setHTTPBody:postBody];
    [[NSURLConnection alloc] initWithRequest:req delegate:nil];
    [pushToken release];
}
- (void)application:(UIApplication *)app didFailToRegisterForRemoteNotificationsWithError:(NSError *)err {
    NSString *str = [NSString stringWithFormat: @"Error: %@", err]; NSLog(@"%@", str);
}

- (void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo {
    for (id key in userInfo) {
        
        NSLog(@"key: %@, value: %@", key, [userInfo objectForKey:key]);
    }
    NSString *alertTitle = @"";
    NSString *type = [userInfo objectForKey:@"type"];
    if([type isEqualToString:@"online_name_approved"])
    {
        alertTitle = @"Online Name Approved";
    }else if([type isEqualToString:@"new_record"])
    {
        alertTitle = @"New Record Set";
    }
    
    UIAlertView* myAlertView = [[UIAlertView alloc] initWithTitle:alertTitle message: [[userInfo objectForKey:@"aps"] objectForKey:@"alert"] delegate: self cancelButtonTitle: @"OK" otherButtonTitles: nil, nil];
    [myAlertView show];
    [myAlertView release];
    
}

- (void)applicationWillResignActive:(UIApplication *)application {
	[[CCDirector sharedDirector] pause];
}

- (void)applicationDidBecomeActive:(UIApplication *)application {
	[[CCDirector sharedDirector] resume];
}

- (void)applicationDidReceiveMemoryWarning:(UIApplication *)application {
	[[CCDirector sharedDirector] purgeCachedData];
}

-(void) applicationDidEnterBackground:(UIApplication*)application {
	[[CCDirector sharedDirector] stopAnimation];
}

-(void) applicationWillEnterForeground:(UIApplication*)application {
	[[CCDirector sharedDirector] startAnimation];
}

- (void)applicationWillTerminate:(UIApplication *)application {
	CCDirector *director = [CCDirector sharedDirector];
	
	[[director openGLView] removeFromSuperview];
	
	[viewController release];
	
	[window release];
	
	[director end];	
}

- (void)applicationSignificantTimeChange:(UIApplication *)application {
	[[CCDirector sharedDirector] setNextDeltaTimeZero:YES];
}

- (void)dealloc {
	[[CCDirector sharedDirector] end];
	[window release];
	[super dealloc];
}

@end
