//
//  EventDetailsViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/19/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class UserGroup;
@class LoadingView;

enum {
	EventStatusSection,
    EventDetailsSection2,
	InvitePeopleSection2,
	DetailsSection
};
enum {
	EventStatusCell,
	YourStatusCell
};
enum {
	EventNameCell2,
	EventTimeCell2,
	EventLocationCell2
};
enum {
	NeedStatusCell,
	PeopleStatusCell,
	NotifyMeCell
};
enum {
	EventDetailsCell2
};

@interface EventDetailsViewController : UITableViewController <NSXMLParserDelegate>{
	NSMutableDictionary *memberItem;
	NSMutableDictionary *eventDetails;
	NSMutableArray *memberlist;
	NSString *event_id;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentName, *currentWhen, *currentWhere;
	NSMutableString *currentNeed, *currentDetails, *currentActive;
	NSMutableString *currentCannotInvite, *currentCannotBring;
	NSMutableString *currentMemberStatus, *currentMemberGuests, *currentMemberName;
	NSMutableString *currentStatus, *currentGuests, *currentNotifyWhen;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) NSMutableDictionary *eventDetails;
@property (nonatomic, retain) NSMutableArray *memberlist;
@property (nonatomic, retain) NSString *event_id;

@property (nonatomic, retain) LoadingView *loadingView;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(void)refreshData;
-(void)refreshView;
-(void)startInvite;
-(void)updateMyStatus:(int)status :(int)guests;
-(void)updateMyNotify:(int)notify;

@end
