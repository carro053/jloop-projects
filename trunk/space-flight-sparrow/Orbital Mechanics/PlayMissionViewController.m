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


#import "PlayMissionViewController.h"
#import "GameConfig.h"
#import "LGViewHUD.h"

LGViewHUD *PlayMissionHud;

@implementation PlayMissionViewController
@synthesize fuelIndicator;
@synthesize cocos2dView;
@synthesize fidoButton;

@synthesize mission_id;
@synthesize online;
@synthesize play;
@synthesize gravity;

- (IBAction)goPressed:(id)sender {
    play = YES;
}

- (IBAction)backPressed:(id)sender {
    if(online)
    {
        PlayMissionHud.bottomText=@"Missions";
        [PlayMissionHud setHidden:NO];
        [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(dismissController) userInfo:nil repeats:NO];
    }else{
        [self dismissViewControllerAnimated:YES completion:nil];
    }
}

- (IBAction)gravityPressed:(id)sender {
    gravity = YES;
}

    
- (void)dismissController {
    [self dismissViewControllerAnimated:YES completion:nil];
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
}

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
}




@end