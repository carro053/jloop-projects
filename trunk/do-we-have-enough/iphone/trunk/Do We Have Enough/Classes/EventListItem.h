//
//  EventListItem.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/27/09.
//  Copyright 2009 JLOOP. All rights reserved.
//
#define kEventKey @"event"
#define kEventName @"event_name"
#define kEventID @"event_id"
#define kActive @"active"
#define kEventWhen @"event_when"
#define kMembersIn @"members_in"
#define kEventNeed @"event_need"

#import <Foundation/Foundation.h>


@interface EventListItem : NSObject {
	int					number;
	NSString			*eventName;
	NSString			*eventID;
	int				active;
	NSString			*eventWhen;
	int					membersIn;
	int					eventNeed;
}
@property int number;
@property (nonatomic, retain) NSString *eventName;
@property (nonatomic, retain) NSString *eventID;
@property int active;
@property (nonatomic, retain) NSString *eventWhen;
@property int membersIn;
@property int eventNeed;

@end
