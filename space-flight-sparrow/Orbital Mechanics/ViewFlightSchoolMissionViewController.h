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
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;

@property (strong, nonatomic) IBOutlet UITextView *description;
@property (strong, nonatomic) IBOutlet UINavigationBar *navBar;
@property (strong, nonatomic) IBOutlet UINavigationItem *navBarItem;
- (IBAction)backPressed:(id)sender;
- (IBAction)startPressed:(id)sender;

@property int mission_id;

@end
