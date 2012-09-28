//
//  YourMissionsViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "EditMissionViewController.h"
#import "PlayMissionViewController.h"

@class Reachability;

@interface YourMissionsViewController : UIViewController <UITableViewDataSource,UITableViewDelegate,UIAlertViewDelegate>
{
    EditMissionViewController *_editMissionViewController;
    PlayMissionViewController *_playMissionViewController;
    Reachability* internetReachable;
    Reachability* hostReachable;
}

@property (retain) EditMissionViewController *editMissionViewController;
@property (retain) PlayMissionViewController *playMissionViewController;
@property (retain, nonatomic) IBOutlet UITableView *missionList;

- (IBAction)newMissionPressed:(id)sender;
- (IBAction)backPressed:(id)sender;
- (IBAction)submitPressed:(id)sender;
- (IBAction)playPressed:(id)sender;
- (IBAction)editPressed:(id)sender;

-(void) checkNetworkStatus:(NSNotification *)notice;


@end
