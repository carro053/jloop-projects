//
//  ChooseUsersController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/15/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AddressBook/AddressBook.h>
#import <AddressBookUI/AddressBookUI.h>
@class ChooseGroupController;
@class CreateEventViewController;
@class EditableCell;

enum {
    GroupNameSection,
	GroupMemberSection
};

@interface ChooseUsersController : UITableViewController <ABPeoplePickerNavigationControllerDelegate, UIActionSheetDelegate, UITextFieldDelegate> {
	CreateEventViewController *parentController2;
	ChooseGroupController *parentController;
	EditableCell *_groupNameField;
	UITextField *textFieldBeingEdited;
	NSString *myParent;
}
@property (nonatomic, retain) CreateEventViewController *parentController2;
@property (nonatomic, retain) ChooseGroupController *parentController;
@property (nonatomic, retain) EditableCell *groupNameField;
@property (nonatomic, retain) UITextField *textFieldBeingEdited;
@property (nonatomic, retain) NSString *myParent;
-(IBAction)showPicker:(id)sender;
-(IBAction)showActionSheet:(id)sender;
-(IBAction)addEmailPressed:(id)sender;

- (EditableCell *)newEditableCellWithTag:(NSInteger)tag;
@end
