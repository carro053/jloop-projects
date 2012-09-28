//
//  ViewFlightSchoolMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 7/4/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PlayFlightSchoolMissionViewController.h"

@interface ViewFlightSchoolMissionViewController : UIViewController {
    int mission_id;
    PlayFlightSchoolMissionViewController *_playFlightSchoolMissionViewController;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;

@property (retain, nonatomic) IBOutlet UITextView *description;
@property (retain, nonatomic) IBOutlet UINavigationBar *navBar;
@property (retain, nonatomic) IBOutlet UINavigationItem *navBarItem;
@property (retain) PlayFlightSchoolMissionViewController *playFlightSchoolMissionViewController;
- (IBAction)backPressed:(id)sender;
- (IBAction)startPressed:(id)sender;

@property int mission_id;

@end
