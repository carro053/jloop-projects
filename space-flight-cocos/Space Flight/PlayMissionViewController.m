//
//  RootViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

//
// RootViewController + iAd
// If you want to support iAd, use this class as the controller of your iAd
//

#import "cocos2d.h"

#import "PlayMissionViewController.h"
#import "GameConfig.h"
#import "PlayMissionScene.h"
#import "LGViewHUD.h"
#import "Constants.h"

LGViewHUD *PlayMissionHud;
EAGLView *glView;
bool graphed = FALSE;

@implementation PlayMissionViewController
@synthesize fuelIndicator;
@synthesize cocos2dView;
@synthesize fidoButton;

@synthesize mission_id;
@synthesize online;
@synthesize play;
@synthesize graphIt;
@synthesize gravity;

@synthesize scatterPlot;

- (IBAction)goPressed:(id)sender {
    play = YES;
    [self resetGraph];
}
- (IBAction)graphPressed:(id)sender {
    graphIt = YES;
}

- (IBAction)backPressed:(id)sender {
    if(online)
    {
        PlayMissionHud.bottomText=@"Missions";
        [PlayMissionHud setHidden:NO];
        [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(dismissController) userInfo:nil repeats:NO];
    }else{
        [self dismissModalViewControllerAnimated:YES];
    }
}

- (IBAction)gravityPressed:(id)sender {
    gravity = YES;
}

-(void) resetGraph {
    
    graphed = FALSE;
    self.scatterPlot.graph = nil;
    [self.scatterPlot.graphData removeAllObjects];
    _graphHostingView.hidden = YES;
    _graphHostingView.userInteractionEnabled = NO;
}


- (void)graphThis:(NSMutableArray *)shipPaths {
    if(graphed)
    {
        [self resetGraph];
    }else{
        graphed = TRUE;
        float xMax = 0.0;
        float yMax = 0.0;
        int latest = 1;
        NSMutableArray *plotArray = [[NSMutableArray alloc] init];
        for (NSMutableArray *aShipPath in shipPaths) {
            double previousTotalTravelTime = 0.0;
            double totalThrustUsed = 0.0;
            NSString *identifier = [NSString stringWithFormat:@"Attempt #%d",latest];
            NSMutableArray *data = [NSMutableArray array];
            int pointCount = 0;
            for(NSDictionary *shipPoint in aShipPath)
            {
                pointCount++;
                totalThrustUsed += [[shipPoint objectForKey:@"thrust_power"] doubleValue];
                //NSLog(@"%f",totalThrustUsed);
                if([[shipPoint objectForKey:@"total_travel_time"] doubleValue] - previousTotalTravelTime >= 0.1 || pointCount == [aShipPath count] || [[shipPoint objectForKey:@"total_fuel_used"] doubleValue] > maxFuel)
                {
                    [data addObject:[NSValue valueWithCGPoint:CGPointMake(previousTotalTravelTime, totalThrustUsed)]];
                    if(previousTotalTravelTime > xMax) xMax = previousTotalTravelTime;
                    if(totalThrustUsed > yMax) yMax = totalThrustUsed;
                    previousTotalTravelTime += 0.1;
                    totalThrustUsed = 0.0;
                }
                if([[shipPoint objectForKey:@"total_fuel_used"] doubleValue] > maxFuel)
                {
                    break;
                }
            }
            NSDictionary *dict = [NSDictionary dictionaryWithObjectsAndKeys:identifier, @"PLOT_IDENTIFIER", data, @"PLOT_DATA",[NSNumber numberWithInt:([shipPaths count] + 1 - latest)],@"PLOT_LINE_STYLE",nil];
            [plotArray addObject:dict];
            latest++;
        }
        NSLog(@"OK");
        self.scatterPlot = [[FuelUsedScatterPlot alloc] initWithHostingView:_graphHostingView andData:plotArray];
        [self.scatterPlot initializePlotWithXMin:0.0 xMax:xMax + 0.1 yMin:0.0 yMax:yMax + 5.0];
        _graphHostingView.hidden = NO;
        _graphHostingView.userInteractionEnabled = YES;
    }
}

    
- (void)dismissController {
    [self dismissModalViewControllerAnimated:YES];
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId withOnline:(BOOL)isOnline
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    self.mission_id = missionId;
    self.online = isOnline;
    self.play = NO;
    if (self) {
        
    }
    
    return self;
}

- (void)setupCocos2D {
    glView = [EAGLView viewWithFrame:self.cocos2dView.bounds
                                   pixelFormat:kEAGLColorFormatRGB565	// kEAGLColorFormatRGBA8
                                   depthFormat:0                        // GL_DEPTH_COMPONENT16_OES
                        ];
    
    [glView setMultipleTouchEnabled:YES];
    glView.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight;
    [self.cocos2dView insertSubview:glView atIndex:0];
    [[CCDirector sharedDirector] setOpenGLView:glView];
    if( ! [[CCDirector sharedDirector] enableRetinaDisplay:YES] )
        CCLOG(@"Retina Display Not supported");
    CCScene *scene = [PlayMissionScene sceneWithId:self.mission_id online:self.online viewController:self];
    [[CCDirector sharedDirector] runWithScene:scene];
}

- (void) viewWillAppear:(BOOL)animated
{
    PlayMissionHud = [LGViewHUD defaultHUD];
    PlayMissionHud.activityIndicatorOn=YES;
    PlayMissionHud.topText=@"Updating";
    PlayMissionHud.bottomText=@"Mission Data";
    [PlayMissionHud showInView:self.view];
    [PlayMissionHud setHidden:YES];
    [self.parentViewController.navigationController setNavigationBarHidden:YES animated:NO];
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [PlayMissionHud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}

- (void)viewDidLoad {
    [super viewDidLoad];    
    [self setupCocos2D];
}


/*
 // The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
 - (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
 if ((self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil])) {
 // Custom initialization
 }
 return self;
 }
 */

/*
 // Implement loadView to create a view hierarchy programmatically, without using a nib.
 - (void)loadView {
 }
 */

/*
 // Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
 - (void)viewDidLoad {
 [super viewDidLoad];
 }
 */


// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
	
	//
	// There are 2 ways to support auto-rotation:
	//  - The OpenGL / cocos2d way
	//     - Faster, but doesn't rotate the UIKit objects
	//  - The ViewController way
	//    - A bit slower, but the UiKit objects are placed in the right place
	//
	
#if GAME_AUTOROTATION==kGameAutorotationNone
	//
	// EAGLView won't be autorotated.
	// Since this method should return YES in at least 1 orientation, 
	// we return YES only in the Portrait orientation
	//
	return ( interfaceOrientation == UIInterfaceOrientationPortrait );
	
#elif GAME_AUTOROTATION==kGameAutorotationCCDirector
	//
	// EAGLView will be rotated by cocos2d
	//
	// Sample: Autorotate only in landscape mode
	//
	if( interfaceOrientation == UIInterfaceOrientationLandscapeLeft ) {
		[[CCDirector sharedDirector] setDeviceOrientation: kCCDeviceOrientationLandscapeRight];
	} else if( interfaceOrientation == UIInterfaceOrientationLandscapeRight) {
		[[CCDirector sharedDirector] setDeviceOrientation: kCCDeviceOrientationLandscapeLeft];
	}
	
	// Since this method should return YES in at least 1 orientation, 
	// we return YES only in the Portrait orientation
	return ( interfaceOrientation == UIInterfaceOrientationPortrait );
	
#elif GAME_AUTOROTATION == kGameAutorotationUIViewController
	//
	// EAGLView will be rotated by the UIViewController
	//
	// Sample: Autorotate only in landscpe mode
	//
	// return YES for the supported orientations
	
	return ( UIInterfaceOrientationIsLandscape( interfaceOrientation ) );
	
#else
#error Unknown value in GAME_AUTOROTATION
	
#endif // GAME_AUTOROTATION
	
	
	// Shold not happen
	return NO;
}

//
// This callback only will be called when GAME_AUTOROTATION == kGameAutorotationUIViewController
//
/*
#if GAME_AUTOROTATION == kGameAutorotationUIViewController
-(void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration
{
	//
	// Assuming that the main window has the size of the screen
	// BUG: This won't work if the EAGLView is not fullscreen
	///
	CGRect screenRect = [[UIScreen mainScreen] bounds];
	CGRect rect = CGRectZero;
    
	
	if(toInterfaceOrientation == UIInterfaceOrientationPortrait || toInterfaceOrientation == UIInterfaceOrientationPortraitUpsideDown)		
		rect = screenRect;
	
	else if(toInterfaceOrientation == UIInterfaceOrientationLandscapeLeft || toInterfaceOrientation == UIInterfaceOrientationLandscapeRight)
		rect.size = CGSizeMake( screenRect.size.height, screenRect.size.width );
	
	CCDirector *director = [CCDirector sharedDirector];
	EAGLView *glView = [director openGLView];
	//float contentScaleFactor = [director contentScaleFactor];
	
	if( contentScaleFactor != 1 ) {
		rect.size.width *= contentScaleFactor;
		rect.size.height *= contentScaleFactor;
	}
	glView.frame = rect;
}
#endif // GAME_AUTOROTATION == kGameAutorotationUIViewController
*/

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [self setFuelIndicator:nil];
    [self setCocos2dView:nil];
    [self setFidoButton:nil];
    [super viewDidUnload];
}

- (void)viewDidDisappear:(BOOL)animated {
    [super viewDidDisappear:animated];
    [[CCDirector sharedDirector] end];
    [glView release];
}


- (void)dealloc {
    [fuelIndicator release];
    [cocos2dView release];
    [fidoButton release];
    [super dealloc];
}


@end