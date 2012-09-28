//
//  HelloWorldLayer.m
//  lobsterlooter
//
//  Created by Michael Stratford on 5/15/12.
//  Copyright JLOOP 2012. All rights reserved.
//


#import "BlankScene.h"

@implementation BlankScene


+(CCScene *) scene
{
	CCScene *scene = [CCScene node];
	BlankScene *layer = [BlankScene node];
    
	[scene addChild: layer];
    
	return scene;
}

-(id) init
{
	if( (self=[super init]) ) {
        
	}
	return self;
}

- (void) dealloc
{
	[super dealloc];
}

@end
