#import "cocos2d.h"
#import "EditMissionScene.h"

@interface CCGravityField : CCLayer {
    EditMissionScene *parentScene;
}

@property (nonatomic,retain) EditMissionScene *parentScene;

+(CCGravityField *) layerWithParent:(CCLayer *)theParent;

@end