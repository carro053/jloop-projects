//
//  EventMemberListViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/23/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "EventDetailsViewController.h"

enum {
	InUsersSection,
	FiftyUsersSection,
	OutUsersSection,
	UnknownUsersSection
};

@interface EventMemberListViewController : UITableViewController {
	NSMutableArray *memberlist;
	NSMutableArray *inlist;
	NSMutableArray *fiftylist;
	NSMutableArray *outlist;
	NSMutableArray *unknownlist;
}

-(IBAction)refresh:(id)sender;

@property (nonatomic, retain) NSMutableArray *memberlist;
@property (nonatomic, retain) NSMutableArray *inlist;
@property (nonatomic, retain) NSMutableArray *fiftylist;
@property (nonatomic, retain) NSMutableArray *outlist;
@property (nonatomic, retain) NSMutableArray *unknownlist;

@property (nonatomic, retain) EventDetailsViewController *eventDetailsController; //reference to the controller that pushes this one

@end
