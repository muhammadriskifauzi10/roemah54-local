USE [roemah54]
GO
/****** Object:  Table [dbo].[katering]    Script Date: 11/13/2024 15:18:16 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
SET ANSI_PADDING ON
GO
CREATE TABLE [dbo].[katering](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[dari] [date] NULL,
	[sampai] [date] NULL,
	[jenis_order] [varchar](50) NULL,
	[jumlah_porsi] [bigint] NULL,
	[lokasi_id] [int] NULL,
	[harga] [bigint] NULL,
	[operator_id] [int] NULL,
	[created_at] [datetime] NULL,
	[updated_at] [datetime] NULL,
 CONSTRAINT [PK_katering] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
SET ANSI_PADDING OFF
GO
