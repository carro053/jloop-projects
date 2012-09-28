//
//  PlayFlightSchoolMissionViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

//
// RootViewController + iAd
// If you want to support iAd, use this class as the controller of your iAd
//


#import "PlayFlightSchoolMissionViewController.h"
#import "GameConfig.h"
#import "LGViewHUD.h"
#import "GameController.h"

LGViewHUD *PlayMissionHud;

@implementation PlayFlightSchoolMissionViewController

@synthesize fuelIndicator;
@synthesize goButton;
@synthesize backButton;
@synthesize fidoButton;
@synthesize toolbar;

@synthesize mission_id;
@synthesize play;
@synthesize gravity;
@synthesize gameView;

- (IBAction)goPressed:(id)sender {
    if([goButton.title isEqualToString:@"Next"])
    {
        [self dismissViewControllerAnimated:YES completion:nil];
    }else{
        play = YES;
    }
}

- (IBAction)backPressed:(id)sender {
    [self dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)gravityPressed:(id)sender {
    gravity = YES;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    self.mission_id = missionId;
    self.play = NO;
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void) viewWillAppear:(BOOL)animated
{
    [SPStage setSupportHighResolutions:YES];
    [SPAudioEngine start];
    GameController *game = [[GameController alloc] initWithWidth:gameView.bounds.size.width height:gameView.bounds.size.height];
    gameView.stage = game;
    gameView.frameRate = 60;
    gameView.multipleTouchEnabled = YES;
    [gameView start];
    if(mission_id < 15)
    {
        NSArray *toolBarArray = toolbar.items;
        NSMutableArray *newToolBarArray = [NSMutableArray arrayWithArray:toolBarArray];
        [newToolBarArray removeObjectAtIndex:4];
        NSArray *finalTabBarArray =[[NSArray alloc] initWithObjects:newToolBarArray, nil];
        [toolbar setItems:[finalTabBarArray objectAtIndex:0] animated:NO];
    }
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

- (void)viewDidDisappear:(BOOL)animated {
    [gameView stop];
    gameView.stage = nil;
    gameView = nil;
    [SPAudioEngine stop];
    
    [super viewDidDisappear:animated];
}

- (void)viewDidLoad {
    [super viewDidLoad];
    
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onTransitionToInactive:) name:UIApplicationWillResignActiveNotification object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(onTransitionToActive:) name:UIApplicationDidBecomeActiveNotification object:nil];
    
}
#pragma mark -
#pragma mark Notifications

- (void)onTransitionToActive:(NSNotification *)sender {
    [gameView start];
    
    [SPAudioEngine start];
}

- (void)onTransitionToInactive:(NSNotification *)sender {
    [gameView stop];
    
    [SPAudioEngine stop];
}


- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [self setFuelIndicator:nil];
    [self setGoButton:nil];
    [self setBackButton:nil];
    [self setFidoButton:nil];
    [self setToolbar:nil];
    [self setGameView:nil];
    [super viewDidUnload];
}

-(void)dealloc {
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIApplicationDidBecomeActiveNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIApplicationWillResignActiveNotification object:nil];
    
}



@end