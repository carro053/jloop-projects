//
//  ViewOnlineMissionViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/19/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "PlayMissionViewController.h"
#import "ViewMissionSolutionViewController.h"

@interface ViewOnlineMissionViewController : UIViewController <UITableViewDataSource, UITableViewDelegate>
{
    int mission_id;
    PlayMissionViewController *_playMissionViewController;
    ViewMissionSolutionViewController *_viewMissionSolutionViewController;
}

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil withMissionId:(int)missionId;

- (IBAction)backPressed:(id)sender;
- (IBAction)playPressed:(id)sender;
- (IBAction)plusOnePressed:(id)sender;
- (IBAction)minusOnePressed:(id)sender;

@property (strong, nonatomic) IBOutlet UINavigationItem *missionTitle;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *minusOne;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *plusOne;

@property (strong, nonatomic) IBOutlet UITableView *solutionList;
@property (strong) PlayMissionViewController *playMissionViewController;
@property (strong) ViewMissionSolutionViewController *viewMissionSolutionViewController;

@property int mission_id;

@end
