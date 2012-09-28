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
@property (retain, nonatomic) IBOutlet UIProgressView *fuelIndicator;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *goButton;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *backButton;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *fidoButton;
@property (retain, nonatomic) IBOutlet UIView *cocos2dView;
@property (retain, nonatomic) IBOutlet UIToolbar *toolbar;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;


@property int mission_id;
@property BOOL play;
@property BOOL gravity;

@end
