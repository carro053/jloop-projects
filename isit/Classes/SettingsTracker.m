//
//  SettingsTracker.m
//  IsIt
//
//  Created by Michael Stratford on 12/07/10.
//

#import "SettingsTracker.h"


@implementation SettingsTracker
@synthesize overlayStartsOn;
@synthesize colorMode;
@synthesize isInited;

- (NSString *)dataFilePath
{
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
	NSString *documentsDirectory = [paths objectAtIndex:0];
	return [documentsDirectory stringByAppendingPathComponent:kFilename];
}
-(void)saveOverlay:(NSString *)overlayStarts
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:overlayStarts];
	[array addObject:colorMode];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.overlayStartsOn = overlayStarts;
}
-(void)saveColorMode:(NSString *)color
{
	if (!isInited) [self initData];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:overlayStartsOn];
	[array addObject:color];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
	self.colorMode = color;
}

-(void)initData
{
	
	NSString *filePath = [self dataFilePath];
	if ([[NSFileManager defaultManager] fileExistsAtPath:filePath])
	{
		NSMutableArray *array = [[NSMutableArray alloc] initWithContentsOfFile:filePath];
		NSString *overlayStarts = [array objectAtIndex:0];
		NSString *color = [array objectAtIndex:1];
		self.overlayStartsOn = overlayStarts;
		self.colorMode = color;
		[array autorelease];
		[self setIsInited:YES];
	} else {
		[self resetData];
		[self setIsInited:YES];
	}

}
-(void)resetData
{
	NSString *overlayStarts = [[NSString alloc] initWithString:@"1"];
	NSString *color = [[NSString alloc] initWithString:@"0"];
	self.overlayStartsOn = overlayStarts;
	self.colorMode = color;
	[overlayStarts release];
	[color release];
	NSMutableArray *array = [[NSMutableArray alloc] init];
	[array addObject:overlayStartsOn];
	[array addObject:colorMode];
	[array writeToFile:[self dataFilePath] atomically:YES];
	[array release];
}

@end
