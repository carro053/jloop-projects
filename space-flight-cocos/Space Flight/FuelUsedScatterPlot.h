//
//  FuelUsedScatterPlot.h
//  Space Flight
//
//  Created by Michael Stratford on 4/9/13.
//  Copyright (c) 2013 JLOOP. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "CorePlot-CocoaTouch.h"

@interface FuelUsedScatterPlot : NSObject<CPTScatterPlotDataSource>{
    CPTGraphHostingView *_hostingView;
    CPTXYGraph *_graph;
    NSMutableArray *_graphData;
}

@property (nonatomic, retain) CPTGraphHostingView *hostingView;
@property (nonatomic, retain) CPTXYGraph *graph;
@property (nonatomic, retain) NSMutableArray *graphData;

// Method to create this object and attach it to it's hosting view.
-(id)initWithHostingView:(CPTGraphHostingView *)hostingView andData:(NSMutableArray *)data;

// Specific code that creates the scatter plot.
-(void)initializePlotWithXMin:(float)xAxisMin xMax:(float)xAxisMax yMin:(float)yAxisMin yMax:(float)yAxisMax;

@end
