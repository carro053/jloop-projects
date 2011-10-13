//
//  ChooseGroupController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class CreateEventViewController;

@class UserGroup;
//  Constants representing the various sections of our grouped table view.
//
enum {
    NewGroupSection,
	PastGroupSection
};

@interface ChooseGroupController : UITableViewController {
	NSMutableArray *grouplist;
	UserGroup *newGroup;
	CreateEventViewController *parentController;
	NSIndexPath	*lastIndexPath;
}
@property (nonatomic, retain) NSMutableArray *grouplist;
@property (nonatomic, retain) UserGroup *newGroup;
@property (nonatomic, retain) CreateEventViewController *parentController;
@property (nonatomic, retain) NSIndexPath *lastIndexPath;
@end
