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


#import "EditMissionViewController.h"
#import "Constants.h"
#import "TestFlight.h"


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
        [TestFlight passCheckpoint:@"Edit Mission"];
    }
    return self;
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
    [fuelSlider setValue:(total_fuel - minFuel) / (maxFuel - minFuel)];
    fuelText.title = [NSString stringWithFormat:@"%dkg",total_fuel];
    /*
    self.addPlanetOverlay.layer.cornerRadius = 10;
    self.addPlanetOverlay.layer.masksToBounds = YES;
    self.addAntiGravityOverlay.layer.cornerRadius = 10;
    self.addAntiGravityOverlay.layer.masksToBounds = YES;
     */
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
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
}




@end