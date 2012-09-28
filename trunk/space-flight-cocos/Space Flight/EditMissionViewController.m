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

#import "EditMissionViewController.h"
#import "GameConfig.h"
#import "EditMissionScene.h"
#import "Constants.h"
#import <QuartzCore/QuartzCore.h>

EAGLView *glView;

int total_fuel;

@implementation EditMissionViewController

@synthesize fuelText;
@synthesize fuelSlider;
@synthesize cocos2dView;
@synthesize fidoButton;
@synthesize addPlanetOverlay;
@synthesize planetRadius;
@synthesize planetDensity;
@synthesize planetMoon;
@synthesize antiGravityRadius;
@synthesize antiGravityDensity;
@synthesize addAntiGravityOverlay;
@synthesize mission_id;

@synthesize cancel;
@synthesize save;
@synthesize fuel;
@synthesize well;
@synthesize astronaut;
@synthesize antiGravity;
@synthesize planet;
@synthesize gravityField;



- (IBAction)wellPressed:(id)sender {
    well = YES;
}

- (IBAction)cancelPressed:(id)sender {
    cancel = YES;
}

- (IBAction)savePressed:(id)sender {
    save = YES;
}

- (IBAction)fuelPressed:(id)sender {
    fuel = YES;
}

- (IBAction)astronautPressed:(id)sender {
    astronaut = YES;
}

- (IBAction)antiGravityPressed:(id)sender {
    antiGravityRadius.value = 0.5;
    antiGravityDensity.value = 0.5;
    addAntiGravityOverlay.hidden = NO;
    addPlanetOverlay.hidden = YES;
}

- (IBAction)planetPressed:(id)sender {
    planetRadius.value = 0.5;
    planetDensity.value = 0.5;
    planetMoon.on = NO;
    addPlanetOverlay.hidden = NO;
    addAntiGravityOverlay.hidden = YES;
}

- (IBAction)gravityFieldPressed:(id)sender {
    gravityField = YES;
}

- (IBAction)fuelChanged:(id)sender {
    
    UISlider *slider = (UISlider *)sender;
    int total_fuel = round(minFuel + 10 * round(slider.value * (maxFuel - minFuel) / 10));
    fuelText.title = [NSString stringWithFormat:@"%dkg",total_fuel];
}

- (IBAction)addPlanetPressed:(id)sender {
    planet = YES;
    addPlanetOverlay.hidden = YES;
}

- (IBAction)addAntiGravityPressed:(id)sender {
    antiGravity = YES;
    addAntiGravityOverlay.hidden = YES;
}

- (IBAction)cancelAddPlanetPressed:(id)sender {
    addPlanetOverlay.hidden = YES;
}

- (IBAction)cancelAddAntiGravityPressed:(id)sender {
    addAntiGravityOverlay.hidden = YES;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    self.mission_id = missionId;
    if (self) {
        NSArray *editFilePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *editFileDirectory = [[NSString alloc] initWithFormat:@"%@",[editFilePaths objectAtIndex:0]];
        NSString *editFileName = [[NSString alloc] initWithFormat:@"%@/CustomMission_%d.plist", editFileDirectory, missionId];
        //read plist
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:editFileName];
        NSArray *keys = [dict allKeys];
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"total_fuel"])
                total_fuel = [[dict objectForKey:key] intValue];
        }
        [editFileDirectory release];
        [editFileName release];
        [TestFlight passCheckpoint:@"Edit Mission"];
    }
    return self;
}

- (void)setupCocos2D {
    [[CCDirector sharedDirector] end];
    NSLog(@"SetupMissionID:%d",self.mission_id);
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
    CCScene *scene = [EditMissionScene sceneWithId:self.mission_id viewController:self];
    [[CCDirector sharedDirector] runWithScene:scene];
}

- (void) viewWillAppear:(BOOL)animated
{
    [self.parentViewController.navigationController setNavigationBarHidden:YES animated:NO];
    self.addPlanetOverlay.hidden = YES;
    self.addAntiGravityOverlay.hidden = YES;
    [super viewWillAppear:animated];
}

- (void)viewDidLoad {
    [super viewDidLoad];    
    [self setupCocos2D];
    [fuelSlider setValue:(total_fuel - minFuel) / (maxFuel - minFuel)];
    fuelText.title = [NSString stringWithFormat:@"%dkg",total_fuel];
    
    self.addPlanetOverlay.layer.cornerRadius = 10;
    self.addPlanetOverlay.layer.masksToBounds = YES;
    self.addAntiGravityOverlay.layer.cornerRadius = 10;
    self.addAntiGravityOverlay.layer.masksToBounds = YES;
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
	float contentScaleFactor = [director contentScaleFactor];
	
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
    [self setFuelSlider:nil];
    [self setFuelText:nil];
    [self setCocos2dView:nil];
    [self setFidoButton:nil];
    [self setAddPlanetOverlay:nil];
    [self setPlanetRadius:nil];
    [self setPlanetDensity:nil];
    [self setPlanetMoon:nil];
    [self setAntiGravityRadius:nil];
    [self setAntiGravityDensity:nil];
    [self setAddAntiGravityOverlay:nil];
    [super viewDidUnload];
}

- (void)viewDidDisappear:(BOOL)animated {
    [super viewDidDisappear:animated];
    [[CCDirector sharedDirector] end];
    [glView release];
}


- (void)dealloc {
    [fuelSlider release];
    [fuelText release];
    [cocos2dView release];
    [fidoButton release];
    [addPlanetOverlay release];
    [planetRadius release];
    [planetDensity release];
    [planetMoon release];
    [antiGravityRadius release];
    [antiGravityDensity release];
    [addAntiGravityOverlay release];
    [super dealloc];
}


@end