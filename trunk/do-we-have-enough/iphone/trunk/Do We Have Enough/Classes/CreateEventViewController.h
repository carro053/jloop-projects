//
//  CreateEventViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
#define kNewGroupTitle @"<New Group>"

@class UserGroup;
@class EditableCell;
@class LoadingView;

enum {
    EventDetailsSection,
	InvitePeopleSection,
	OptionsSection
};
enum {
	EventNameCell,
	EventLocationCell,
    EventTimeCell
};
enum {
	PeopleNeedCell,
	PeopleInvitedCell
};
enum {
	EventDetailsCell,
	EventOptionsCell
};

@interface CreateEventViewController : UITableViewController <UITextFieldDelegate, UIActionSheetDelegate, NSXMLParserDelegate> {
	NSMutableArray *grouplist;
	NSString *selectedGroupID;
	NSString *eventDetails;
	NSString *eventName;
	NSString *eventLocation;
	BOOL inviteOthers;
	BOOL bringGuests;
	BOOL statusEmail;
	BOOL cancelEmail;
	BOOL whenSet;
	NSDate *statusEmailDate;
	NSDate *cancelEmailDate;
	NSDate *whenDate;
	UserGroup *newGroup;
	EditableCell *_eventNameField;
	EditableCell *_eventLocationField;
	int eventNeed;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentName, *currentID, *currentMembers;
	NSMutableString *submitStatus, *submitEventID;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) NSMutableArray *grouplist;
@property (nonatomic, retain) UserGroup *newGroup;
@property (nonatomic, retain) NSString *selectedGroupID;
@property (nonatomic, retain) NSString *eventDetails;
@property (nonatomic, retain) NSString *eventName;
@property (nonatomic, retain) NSString *eventLocation;
@property (nonatomic, readwrite) BOOL inviteOthers;
@property (nonatomic, readwrite) BOOL bringGuests;
@property (nonatomic, readwrite) BOOL statusEmail;
@property (nonatomic, readwrite) BOOL cancelEmail;
@property (nonatomic, readwrite) BOOL whenSet;
@property (nonatomic, readwrite) int eventNeed;
@property (nonatomic, retain) NSDate *statusEmailDate;
@property (nonatomic, retain) NSDate *cancelEmailDate;
@property (nonatomic, retain) NSDate *whenDate;
@property (nonatomic, retain) EditableCell *eventNameField;
@property (nonatomic, retain) EditableCell *eventLocationField;
@property (nonatomic, retain) LoadingView *loadingView;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(void)postCreateData;
-(void)startCreate;
-(void)resumeCreate;
-(void)kickHome;
-(void)checkValidation;
-(void)storeEventValues;
- (NSTimeInterval)calcTimezoneDiff;
- (EditableCell *)newEditableCellWithTag:(NSInteger)tag;
-(IBAction)selectWhenDatePressed;
-(IBAction)cancel:(id)sender;
@end
