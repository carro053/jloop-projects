//
//  UserGroup.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//
#define kGroupNumberKey	@"Group"
#define kGroupNameKey	@"GroupName"
#define kGroupIDKey		@"GroupID"
#define kGroupMembersKey	@"GroupMembers"
#define kGroupMembersCountKey	@"groupMembersCount"

#import <Foundation/Foundation.h>


@interface UserGroup : NSObject {
	int					number;
	NSString			*groupName; 
	NSString			*groupID;
	NSMutableArray		*groupMembers;
	int					groupMembersCount;
}
@property int number;
@property (nonatomic, retain) NSString *groupName;
@property (nonatomic, retain) NSString *groupID;
@property (nonatomic, retain) NSMutableArray *groupMembers;
@property int groupMembersCount;

@end
