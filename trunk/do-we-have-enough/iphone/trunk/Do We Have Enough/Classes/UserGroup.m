//
//  UserGroup.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "UserGroup.h"


@implementation UserGroup
@synthesize number, groupName, groupID, groupMembers, groupMembersCount;

-(void)dealloc {
	[groupName release];
	[groupID	release];
	[groupMembers release];
	[super dealloc];
}

#pragma mark -
#pragma mark NSCoding
-(void)encodeWithCoder:(NSCoder *)coder {
	[coder encodeInt:self.number forKey:kGroupNumberKey];
	[coder encodeObject:self.groupName forKey:kGroupNameKey];
	[coder encodeObject:self.groupID forKey:kGroupIDKey];
	[coder encodeObject:self.groupMembers forKey:kGroupMembersKey];
	[coder encodeInt:self.groupMembersCount forKey:kGroupMembersCountKey];
}
-(id)initWithCoder:(NSCoder *)coder {
	if (self = [super init]) {
		self.number = [coder decodeIntForKey:kGroupNumberKey];
		self.groupName = [coder decodeObjectForKey:kGroupNameKey];
		self.groupID = [coder decodeObjectForKey:kGroupIDKey];
		self.groupMembers = [coder decodeObjectForKey:kGroupMembersKey];
		self.groupMembersCount = [coder decodeIntForKey:kGroupMembersCountKey];
	}
	return self;
}

@end
