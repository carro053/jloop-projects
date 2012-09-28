//
//  ViewFlightSchoolMissionViewController.m
//  Space Flight
//
//  Created by Michael Stratford on 7/4/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "ViewFlightSchoolMissionViewController.h"
#import "LGViewHUD.h"

@interface ViewFlightSchoolMissionViewController ()

@end

NSString *descriptionText;
LGViewHUD* ViewFlightSchoolMissionHud;

@implementation ViewFlightSchoolMissionViewController
@synthesize description;
@synthesize navBar;
@synthesize navBarItem;
@synthesize mission_id;

@synthesize playFlightSchoolMissionViewController = _playFlightSchoolMissionViewController;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    self.mission_id = missionId;
    if (self) {
        [TestFlight passCheckpoint:[NSString stringWithFormat:@"Flight School Mission:%d",missionId]];
    }
    return self;
}

- (void)viewDidAppear:(BOOL)animated {
    if (_playFlightSchoolMissionViewController != nil) {
        [_playFlightSchoolMissionViewController release];
        _playFlightSchoolMissionViewController = nil;
    }
    [super viewDidAppear:animated];
    
    NSArray *filePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
    NSString *fileDirectory = [[NSString alloc] initWithFormat:@"%@",[filePaths objectAtIndex:0]];
    NSString *fileName = [[NSString alloc] initWithFormat:@"%@/FlightSchool.plist", fileDirectory];
    NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:fileName];
    [fileDirectory release];
    [fileName release];
    if([[dict objectForKey:@"justCompletedMission"] intValue] >= 0)
    {
        [self dismissModalViewControllerAnimated:YES];
    }
}

- (void)viewWillAppear:(BOOL)animated {
    
    NSString* plistPath = [[NSBundle mainBundle] pathForResource:@"FlightSchool" ofType:@"plist"];
    NSDictionary *dict = [NSDictionary dictionaryWithContentsOfFile:plistPath];
    NSArray *missionArray = [dict objectForKey:@"flightSchoolArray"];
    NSDictionary *mission = [missionArray objectAtIndex:self.mission_id];
    navBarItem.title = [mission objectForKey:@"name"];
    description.text = [mission objectForKey:@"description"];
    ViewFlightSchoolMissionHud = [LGViewHUD defaultHUD];
    ViewFlightSchoolMissionHud.activityIndicatorOn=YES;
    ViewFlightSchoolMissionHud.topText=@"Loading";
    ViewFlightSchoolMissionHud.bottomText=@"Mission Data";
    [ViewFlightSchoolMissionHud showInView:self.view];
    [ViewFlightSchoolMissionHud setHidden:YES];
    
    [super viewWillAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [ViewFlightSchoolMissionHud hideWithAnimation:HUDAnimationNone];
    [super viewWillDisappear:animated];
}


- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    [self setDescription:nil];
    [self setNavBar:nil];
    [self setNavBarItem:nil];
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return UIInterfaceOrientationIsLandscape(interfaceOrientation);
}

- (void)dealloc {
    [descriptionText release];
    [navBar release];
    [navBarItem release];
    [description release];
    [super dealloc];
}
- (IBAction)backPressed:(id)sender {
    [self dismissModalViewControllerAnimated:YES];
}

- (IBAction)startPressed:(id)sender {
    ViewFlightSchoolMissionHud.bottomText=@"Mission Data";
    [ViewFlightSchoolMissionHud setHidden:NO];
    [NSTimer scheduledTimerWithTimeInterval:0.01 target:self selector:@selector(goPlay) userInfo:nil repeats:NO];
}

- (void)goPlay {
    if (_playFlightSchoolMissionViewController == nil) {
        self.playFlightSchoolMissionViewController = [[[PlayFlightSchoolMissionViewController alloc] initWithNibName:@"PlayFlightSchoolMissionViewController" bundle:nil withMissionId:mission_id] autorelease];
    }
    [self presentModalViewController:_playFlightSchoolMissionViewController animated:YES];
}
@end
