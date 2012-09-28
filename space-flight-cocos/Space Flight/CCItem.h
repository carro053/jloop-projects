#import "cocos2d.h"

@interface CCItem : CCSprite {
    NSString *itemType;
}
@property (nonatomic,retain) NSString *itemType;
+(id)spriteWithSpriteFrameName:(NSString *)spriteFrameName andType:(NSString *)item_type;


@end