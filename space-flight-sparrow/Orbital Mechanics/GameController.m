//
//  GameController.m
//  AppScaffold
//

#import <OpenGLES/ES1/gl.h>
#import "GameController.h"


@interface GameController ()

@end


@implementation GameController

- (id)initWithWidth:(float)width height:(float)height
{
    if ((self = [super initWithWidth:width height:height]))
    {
        float gameWidth  = width;
        float gameHeight = height;
        
        mGame = [[Game alloc] initWithWidth:gameWidth height:gameHeight];
        
        mGame.pivotX = gameWidth  / 2;
        mGame.pivotY = gameHeight / 2;
        
        mGame.x = width  / 2;
        mGame.y = height / 2;
        
        [self addChild:mGame];
        
    }
    
    return self;
}


- (void)dealloc
{
    
}


@end
