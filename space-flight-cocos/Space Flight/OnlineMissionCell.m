//
//  OnlineMissionCell.m
//  Space Flight
//
//  Created by Michael Stratford on 7/3/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "OnlineMissionCell.h"

@implementation OnlineMissionCell
@synthesize imageView;
@synthesize title;
@synthesize rating;
@synthesize submitted;
@synthesize viewButton;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (void)dealloc {
    [imageView release];
    [title release];
    [rating release];
    [submitted release];
    [viewButton release];
    [super dealloc];
}
@end
