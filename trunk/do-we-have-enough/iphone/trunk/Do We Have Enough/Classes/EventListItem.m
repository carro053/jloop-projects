//
//  EventListItem.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/27/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EventListItem.h"


@implementation EventListItem
@synthesize number, eventName, eventID, active, eventWhen, membersIn, eventNeed;

-(void)dealloc {
	[eventName release];
	[eventID	release];
	[eventWhen release];
	[super dealloc];
}

#pragma mark -
#pragma mark NSCoding
-(void)encodeWithCoder:(NSCoder *)coder {
	[coder encodeInt:self.number forKey:kEventKey];
	[coder encodeObject:self.eventName forKey:kEventName];
	[coder encodeObject:self.eventID forKey:kEventID];
	[coder encodeInt:self.active forKey:kActive];
	[coder encodeObject:self.eventWhen forKey:kEventWhen];
	[coder encodeInt:self.membersIn forKey:kMembersIn];
	[coder encodeInt:self.eventNeed forKey:kEventNeed];
}
-(id)initWithCoder:(NSCoder *)coder {
	if (self = [super init]) {
		self.number = [coder decodeIntForKey:kEventKey];
		self.eventName = [coder decodeObjectForKey:kEventName];
		self.eventID = [coder decodeObjectForKey:kEventID];
		self.active = [coder decodeIntForKey:kActive];
		self.eventWhen = [coder	decodeObjectForKey:kEventWhen];
		self.membersIn = [coder decodeIntForKey:kMembersIn];
		self.eventNeed = [coder decodeIntForKey:kEventNeed];
	}
	return self;
}

@end
