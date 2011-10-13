//
//  SettingsTracker.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "SettingsTracker.h"


@implementation SettingsTracker
@synthesize emailAddress;
@synthesize isValidated;
@synthesize isInited;
@synthesize notifyIn, notifyOut, notifyPush;

- (NSString *)dataFilePath
{
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	return [documentsDirectory stringByAppendingPathComponent:kFilename];
}
-(void)saveEmail:(NSString *)email
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:email];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyPush];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.emailAddress = email;
}
-(void)saveValidation:(NSString *)validation
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:validation];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyPush];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.isValidated = validation;
}
-(void)saveNotifyIn:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notify];
	[array addObject:notifyOut];
	[array addObject:notifyPush];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.notifyIn = notify;
}
-(void)saveNotifyOut:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notify];
	[array addObject:notifyPush];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.notifyOut = notify;
}
-(void)saveNotifyPush:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notify];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.notifyPush = notify;
}

-(void)initData
{
	
	NSString *filePath = [self dataFilePath];
	if ([[NSFileManager defaultManager] fileExistsAtPath:filePath])
	{
		NSMutableArray *array = [[NSMutableArray alloc] initWithContentsOfFile:filePath];
		NSString *myEmail = [array objectAtIndex:0];
		NSString *myValidation = [array objectAtIndex:1];
		NSString *myNotifyIn = [array objectAtIndex:2];
		NSString *myNotifyOut = [array objectAtIndex:3];
		NSString *myNotifyPush = [array objectAtIndex:4];
		self.emailAddress = myEmail;
		self.isValidated = myValidation;
		self.notifyIn = myNotifyIn;
		self.notifyOut = myNotifyOut;
		self.notifyPush = myNotifyPush;
		[array autorelease];
		[self setIsInited:YES];
	} else {
		[self resetData];
		[self setIsInited:YES];
	}

}
-(void)resetData
{
	NSString *tempEmail = [[NSString alloc] initWithString:@"false"];
	NSString *tempValidated = [[NSString alloc] initWithString:@"false"];
	NSString *tempNotifyIn= [[NSString alloc] initWithString:@"0"];
	NSString *tempNotifyOut= [[NSString alloc] initWithString:@"0"];
	NSString *tempNotifyPush= [[NSString alloc] initWithString:@"1"];
	self.emailAddress = tempEmail;
	self.isValidated = tempValidated;
	self.notifyIn = tempNotifyIn;
	self.notifyOut = tempNotifyOut;
	self.notifyPush = tempNotifyPush;
	[tempEmail release];
	[tempValidated release];
	[tempNotifyIn release];
	[tempNotifyOut release];
	[tempNotifyPush release];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyPush];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
}


- (NSString *)retrieveEmail
{
	return self.emailAddress;
}
@end
