//
//  PlayFlightSchoolMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface PlayFlightSchoolMissionViewController : UIViewController {
    int mission_id;
    BOOL play;
    BOOL gravity;
}

- (IBAction)goPressed:(id)sender;
- (IBAction)backPressed:(id)sender;
- (IBAction)gravityPressed:(id)sender;
@property (strong, nonatomic) IBOutlet UIProgressView *fuelIndicator;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *goButton;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *backButton;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *fidoButton;
@property (strong, nonatomic) IBOutlet UIToolbar *toolbar;
@property (strong, nonatomic) IBOutlet SPView *gameView;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;
- (void)onTransitionToActive:(NSNotification *)sender;
- (void)onTransitionToInactive:(NSNotification *)sender;


@property int mission_id;
@property BOOL play;
@property BOOL gravity;

@end
