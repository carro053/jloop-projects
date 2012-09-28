#import "CCItem.h"

@implementation CCItem

@synthesize itemType;

-(id) init {
    if((self=[super init])){
        
    }
    return self;
}

+(id)spriteWithSpriteFrameName:(NSString *)spriteFrameName andType:(NSString *)item_type 
{
    CCSpriteFrame *frame = [[CCSpriteFrameCache sharedSpriteFrameCache] spriteFrameByName:spriteFrameName];
	CCItem *newItem = [self spriteWithSpriteFrame:frame];
    newItem.itemType = item_type;
	NSAssert1(frame!=nil, @"Invalid spriteFrameName: %@", spriteFrameName);
	return newItem;
}

@end