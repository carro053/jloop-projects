//
//  EditMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface EditMissionViewController : UIViewController {
    int mission_id;
    BOOL cancel;
    BOOL save;
    BOOL fuel;
    BOOL well;
    BOOL astronaut;
    BOOL antiGravity;
    BOOL planet;
    BOOL gravityField;
}
- (IBAction)wellPressed:(id)sender;
- (IBAction)cancelPressed:(id)sender;
- (IBAction)savePressed:(id)sender;
- (IBAction)fuelPressed:(id)sender;
- (IBAction)astronautPressed:(id)sender;
- (IBAction)antiGravityPressed:(id)sender;
- (IBAction)planetPressed:(id)sender;
- (IBAction)gravityFieldPressed:(id)sender;
- (IBAction)fuelChanged:(id)sender;
- (IBAction)addPlanetPressed:(id)sender;
- (IBAction)addAntiGravityPressed:(id)sender;
- (IBAction)cancelAddPlanetPressed:(id)sender;
- (IBAction)cancelAddAntiGravityPressed:(id)sender;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;

@property (strong, nonatomic) IBOutlet UIBarButtonItem *fuelText;
@property (strong, nonatomic) IBOutlet UISlider *fuelSlider;
@property (strong, nonatomic) IBOutlet UIView *cocos2dView;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *fidoButton;
@property (strong, nonatomic) IBOutlet UIView *addPlanetOverlay;
@property (strong, nonatomic) IBOutlet UISlider *planetRadius;
@property (strong, nonatomic) IBOutlet UISlider *planetDensity;
@property (strong, nonatomic) IBOutlet UISwitch *planetMoon;
@property (strong, nonatomic) IBOutlet UISlider *antiGravityRadius;
@property (strong, nonatomic) IBOutlet UISlider *antiGravityDensity;
@property (strong, nonatomic) IBOutlet UIView *addAntiGravityOverlay;

@property int mission_id;
@property BOOL cancel;
@property BOOL save;
@property BOOL fuel;
@property BOOL well;
@property BOOL astronaut;
@property BOOL antiGravity;
@property BOOL planet;
@property BOOL gravityField;

@end
