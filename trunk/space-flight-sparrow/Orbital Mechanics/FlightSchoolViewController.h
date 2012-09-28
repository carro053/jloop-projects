//
//  FlightSchoolViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ViewFlightSchoolMissionViewController.h"

@interface FlightSchoolViewController : UIViewController<UITableViewDataSource,UITableViewDelegate> {
	NSMutableArray *missionArray;   // the main data model for our UITableView
	NSMutableDictionary *completedMissions;   // the main data model for our UITableView
}

- (IBAction)backPressed:(id)sender;
@property (strong, nonatomic) IBOutlet UITableView *missionList;
@property (nonatomic, strong) NSMutableArray *missionArray;
@property (nonatomic, strong) NSMutableDictionary *completedMissions;

@end
