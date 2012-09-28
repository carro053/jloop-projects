//
//  PlayMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright JLOOP 2012. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface PlayMissionViewController : UIViewController {
    int mission_id;
    BOOL online;
    BOOL play;
    BOOL gravity;
}
- (IBAction)goPressed:(id)sender;
- (IBAction)backPressed:(id)sender;
- (IBAction)gravityPressed:(id)sender;
@property (retain, nonatomic) IBOutlet UIProgressView *fuelIndicator;
@property (retain, nonatomic) IBOutlet UIView *cocos2dView;
@property (retain, nonatomic) IBOutlet UIBarButtonItem *fidoButton;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId withOnline:(BOOL)isOnline;


@property int mission_id;
@property BOOL online;
@property BOOL play;
@property BOOL gravity;

@end
