//
//  OnlineMissionsViewController.h
//  Space Flight
//
//  Created by Michael Stratford on 6/18/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ViewOnlineMissionViewController.h"
#import "IconDownloader.h"


@interface OnlineMissionsViewController : UIViewController <UITableViewDataSource, UITableViewDelegate,IconDownloaderDelegate, UIScrollViewDelegate>
{
	NSMutableArray *missionArray;   // the main data model for our UITableView
    NSMutableDictionary *imageDownloadsInProgress;  // the set of IconDownloader objects for each app
    
}

@property (strong, nonatomic) IBOutlet UIBarButtonItem *orderButton;
@property (strong, nonatomic) IBOutlet UITableView *missionList;
@property (nonatomic, strong) NSMutableArray *missionArray;
@property (nonatomic, strong) NSMutableDictionary *imageDownloadsInProgress;

- (void)appImageDidLoad:(NSIndexPath *)indexPath;

- (IBAction)backPressed:(id)sender;
- (IBAction)orderPressed:(id)sender;
- (IBAction)viewPressed:(id)sender;

@end
