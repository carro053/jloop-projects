//
//  EventOptionsViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/24/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class CreateEventViewController;
#define kSelectTime @"Click to select notification time."

enum {
    UserOptionsSection,
	NotificationOptionsSection
};
enum {
	InviteOthersCell,
	BringGuestsCell
};
enum {
	StatusEmailCell,
	CancelEmailCell
};

@interface EventOptionsViewController : UITableViewController {
	CreateEventViewController *parentController;
}
@property (nonatomic, retain) CreateEventViewController *parentController;
-(IBAction)selectStatusDatePressed;
-(IBAction)selectCancelDatePressed;
@end
