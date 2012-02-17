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
@synthesize notifyIn, notifyOut, notifyEventChange, appNotifyIn, appNotifyOut, appNotifyEventChange;

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
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
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
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
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
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
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
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.notifyOut = notify;
}
-(void)saveNotifyEventChange:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notify];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.notifyEventChange = notify;
}
-(void)saveAppNotifyIn:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyEventChange];
	[array addObject:notify];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.appNotifyIn = notify;
}
-(void)saveAppNotifyOut:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:notify];
	[array addObject:appNotifyEventChange];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.appNotifyOut = notify;
}
-(void)saveAppNotifyEventChange:(NSString *)notify
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:notify];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.appNotifyEventChange = notify;
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
		NSString *myNotifyEventChange = [array objectAtIndex:4];
		NSString *myAppNotifyIn = [array objectAtIndex:5];
		NSString *myAppNotifyOut = [array objectAtIndex:6];
		NSString *myAppNotifyEventChange = [array objectAtIndex:7];
		self.emailAddress = myEmail;
		self.isValidated = myValidation;
		self.notifyIn = myNotifyIn;
		self.notifyOut = myNotifyOut;
		self.notifyEventChange = myNotifyEventChange;
		self.appNotifyIn = myAppNotifyIn;
		self.appNotifyOut = myAppNotifyOut;
		self.appNotifyEventChange = myAppNotifyEventChange;
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
	NSString *tempNotifyEventChange= [[NSString alloc] initWithString:@"1"];
	NSString *tempAppNotifyIn= [[NSString alloc] initWithString:@"0"];
	NSString *tempAppNotifyOut= [[NSString alloc] initWithString:@"0"];
	NSString *tempAppNotifyEventChange= [[NSString alloc] initWithString:@"1"];
	self.emailAddress = tempEmail;
	self.isValidated = tempValidated;
	self.notifyIn = tempNotifyIn;
	self.notifyOut = tempNotifyOut;
	self.notifyEventChange = tempNotifyEventChange;
	self.appNotifyIn = tempAppNotifyIn;
	self.appNotifyOut = tempAppNotifyOut;
	self.appNotifyEventChange = tempAppNotifyEventChange;
	[tempEmail release];
	[tempValidated release];
	[tempNotifyIn release];
	[tempNotifyOut release];
	[tempNotifyEventChange release];
	[tempAppNotifyIn release];
	[tempAppNotifyOut release];
	[tempAppNotifyEventChange release];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:emailAddress];
	[array addObject:isValidated];
	[array addObject:notifyIn];
	[array addObject:notifyOut];
	[array addObject:notifyEventChange];
	[array addObject:appNotifyIn];
	[array addObject:appNotifyOut];
	[array addObject:appNotifyEventChange];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
}


- (NSString *)retrieveEmail
{
	return self.emailAddress;
}
@end
