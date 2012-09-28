#import "cocos2d.h"
#import "PlayMissionScene.h"
#import "GraphicsGems.h"
#include <stdio.h>
#include <math.h>
typedef Point2 *BezierCurve;
static	double		*Reparameterize();
static	double 		B0(), B1(), B2(), B3();
static	Vector2		ComputeLeftTangent();
static	Vector2		ComputeRightTangent();
static	Vector2		ComputeCenterTangent();
static	double		*ChordLengthParameterize();
static	Vector2		V2AddII();
static	Vector2		V2ScaleIII();
static	Vector2		V2SubII();

@interface PathLayer : CCLayer {
    PlayMissionScene *parentScene;
}

@property (nonatomic,retain) PlayMissionScene *parentScene;

+(PathLayer *) layerWithParent:(CCLayer *)theParent;
-(void) DrawBezierCurveInt:(int)n Curve:(BezierCurve)curve;
-(double) BezierPointT:(double)t Start:(double)start Control1:(double)control_1 Control2:(double)control_2 End:(double)end;
-(void) FitCurvePoints:(Point2 *)d numberOf:(int) nPts theError:(double)error;
-(void) FitCubicPoints:(Point2 *)d First:(int)first Last:(int)last THat1:(Vector2)tHat1 THat2:(Vector2)tHat2 TheError:(double)error;
-(BezierCurve) GenerateBezierPoints:(Point2 *)d First:(int)first Last:(int)last UPrime:(double *)uPrime THat1:(Vector2)tHat1 THat2:(Vector2)tHat2;
-(double *) ReparameterizePoints:(Point2 *)d First:(int)first Last:(int)last Current:(double *)u TheCurve:(BezierCurve)bezCurve;
-(double) NewtonRaphsonRootFindCurve:(BezierCurve)Q Point:(Point2)P Param:(double)u;
-(Point2) BezierIIDegree:(int)degree Point:(Point2 *)V Param:(double)t;
-(void) DrawBezierCurveInt:(int)n Curve:(BezierCurve)curve;
-(double) ComputeMaxErrorPoints:(Point2 *)d First:(int)first Last:(int)last Curve:(BezierCurve)bezCurve Param:(double *)u maxError:(int *)splitPoint;

@end